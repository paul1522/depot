<?php

namespace App\Filament\Pages;

use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use App\Models\Condition;
use App\Models\Item;
use App\Models\Location;
use App\Models\Transaction;
use Carbon\Carbon;
use Exception;
use Filament\Forms;
use Filament\Pages;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;

class InventoryTransactionsPage extends Pages\Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationLabel = 'Inventory Transactions';

    protected static ?string $title = 'Inventory Transactions Admin Report';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.inventory-transactions-page';

    public function getTableQuery(): Builder|Relation
    // Must be public for FilamentExportHeaderAction
    {
        return Transaction::query()
            ->select('transactions.id', 'transactions.item_location_id',
                DB::raw('transactions.quantity as transaction_quantity'),
                DB::raw('transactions.date as transaction_date'),
                'item_locations.item_id', 'item_locations.location_id', 'item_locations.condition_id'
            )
            ->join('item_locations', 'item_locations.id', '=', 'transactions.item_location_id')
            ->join('items', 'items.id', '=', 'item_locations.item_id')
            ->join('locations', 'locations.id', '=', 'item_locations.location_id')
            ->join('conditions', 'conditions.id', '=', 'item_locations.condition_id');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('transaction_date')->date()->sortable(),
            Tables\Columns\TextColumn::make('item_location.item.description')->label('Charter description')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('item_location.item.sbt_item')->label('SBT item prefix')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('item_location.item.key')->label('Charter key')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('item_location.item.supplier_key')->label('Charter supplier key')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('item_location.item.group')->label('Charter group')->sortable(),
            Tables\Columns\TextColumn::make('item_location.item.manufacturer')->label('Manufacturer')->sortable(),
            Tables\Columns\TextColumn::make('item_location.location.name')->label('Location')->sortable(),
            Tables\Columns\TextColumn::make('item_location.condition.name')->label('Condition')->sortable(),
            Tables\Columns\TextColumn::make('transaction_quantity')->label('Qty')->alignRight()->sortable(),
            Tables\Columns\TextColumn::make('description')->label('Reference')->searchable()->sortable(),
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 20, 50, 100];
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'date';
    }

    protected function getDefaultTableSortDirection(): string
    {
        return 'desc';
    }

    protected function shouldPersistTableSortInSession(): bool
    {
        return true;
    }

    protected function shouldPersistTableFiltersInSession(): bool
    {
        return true;
    }

    protected function shouldPersistTableSearchInSession(): bool
    {
        return true;
    }

    /**
     * @throws Exception
     */
    protected function getTableHeaderActions(): array
    {
        return [
            FilamentExportHeaderAction::make('Export')
                ->disableAdditionalColumns()
                ->disableFilterColumns()
                ->fileNamePrefix('Depot Inventory Transactions Report'),
        ];
    }

    /**
     * @throws Exception
     */
    protected function getTableFilters(): array
    {
        return [
            $this->getDateTableFilter(),
            $this->getGroupTableFilter(),
            $this->getManufacturerTableFilter(),
            $this->getLocationTableFilter(),
            $this->getConditionTableFilter(),
        ];
    }

    /**
     * @throws Exception
     */
    private function getDateTableFilter()
    {
        return Tables\Filters\Filter::make('date')
            ->form([
                Forms\Components\DatePicker::make('date_from'),
                Forms\Components\DatePicker::make('date_until'),
            ])
            ->query(function (Builder $query, array $data): Builder {
                return $query
                    ->when(
                        $data['date_from'],
                        fn (Builder $query, $date): Builder => $query->whereDate('transactions.date', '>=', $date),
                    )
                    ->when(
                        $data['date_until'],
                        fn (Builder $query, $date): Builder => $query->whereDate('transactions.date', '<=', $date),
                    );
            })
            ->indicateUsing(function (array $data): ?string {
                if (! $data['date_from'] && ! $data['date_until']) {
                    return null;
                }

                return trim(
                    ($data['date_from'] ? ' From '.Carbon::parse($data['date_from'])->toFormattedDateString() : '').
                    ($data['date_until'] ? ' Until '.Carbon::parse($data['date_until'])->toFormattedDateString() : '')
                );
            });
    }

    /**
     * @throws Exception
     */
    public function getGroupTableFilter(): Tables\Filters\Filter
    {
        return Tables\Filters\Filter::make('group')
            ->form([
                Forms\Components\Select::make('group')->options($this->groupOptions())->placeholder('All'),
            ])
            ->query(function (Builder $query, array $data): Builder {
                if (! $data['group']) {
                    return $query;
                }

                return $query->where('item_location.item.group', '=', $data['group']);
            })
            ->indicateUsing(function (array $data): ?string {
                return $data['group'] ? 'Group: '.$data['group'] : null;
            });
    }

    private function groupOptions(): array
    {
        return Item::distinct()
            ->pluck('group', 'group')
            ->toArray();
    }

    /**
     * @throws Exception
     */
    public function getManufacturerTableFilter(): Tables\Filters\Filter
    {
        return Tables\Filters\Filter::make('manufacturer')
            ->form([
                Forms\Components\Select::make('manufacturer')->options($this->manufacturerOptions())->placeholder('All'),
            ])
            ->query(function (Builder $query, array $data): Builder {
                if (! $data['manufacturer']) {
                    return $query;
                }

                return $query->where('items.manufacturer', '=', $data['manufacturer']);
            })
            ->indicateUsing(function (array $data): ?string {
                return $data['manufacturer'] ? 'Manufacturer: '.$data['manufacturer'] : null;
            });
    }

    private function manufacturerOptions(): array
    {
        return Item::distinct()
            ->pluck('manufacturer', 'manufacturer')
            ->toArray();
    }

    /**
     * @throws Exception
     */
    public function getLocationTableFilter(): Tables\Filters\Filter
    {
        return Tables\Filters\Filter::make('location')->form([
            Forms\Components\Select::make('location')->options($this->locationOptions())->placeholder('All'),
        ])->query(function (Builder $query, array $data): Builder {
            if (! $data['location']) {
                return $query;
            }

            return $query->where('item_locations.location_id', '=', $data['location']);
        })->indicateUsing(function (array $data): ?string {
            return $data['location'] ? 'Location: '.Location::find($data['location'])->name : null;
        });
    }

    private function locationOptions(): array
    {
        return Location::orderBy('name')->pluck('name', 'id')->toArray();
    }

    /**
     * @throws Exception
     */
    public function getConditionTableFilter(): Tables\Filters\Filter
    {
        return Tables\Filters\Filter::make('condition')->form([
            Forms\Components\Select::make('condition')->options($this->conditionOptions())->placeholder('All'),
        ])->query(function (Builder $query, array $data): Builder {
            if (! $data['condition']) {
                return $query;
            }

            return $query->where('item_locations.condition_id', '=', $data['condition']);
        })->indicateUsing(function (array $data): ?string {
            return $data['condition'] ? 'Condition: '.Condition::find($data['condition'])->name : null;
        });
    }

    private function conditionOptions(): array
    {
        return Condition::orderBy('name')->pluck('name', 'id')->toArray();
    }
}
