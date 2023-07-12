<?php

namespace App\Http\Livewire;

use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use App\Models\Condition;
use App\Models\Item;
use App\Models\Transaction;
use Carbon\Carbon;
use Exception;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class InventoryTransactionsReport extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public function render(): View
    {
        return view('livewire.inventory-transactions-report');
    }

    public function getTableQuery(): Builder|Relation
    // Must be public for FilamentExportHeaderAction
    {
        return Transaction::query()
            ->select('transactions.id', DB::raw('transactions.quantity as transaction_quantity'),
                'transactions.item_location_id', 'transactions.date', 'transactions.description')
            ->join('item_locations', 'item_locations.id', '=', 'transactions.item_location_id')
            ->whereIn('item_locations.location_id', request()->user()->locations->pluck('id'));
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

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('date')->date()->sortable(),
            Tables\Columns\TextColumn::make('item_location.item.description')->label('Description')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('item_location.item.key')->label('Key')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('item_location.item.supplier_key')->label('Supplier key')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('item_location.item.group')->label('Group')->sortable(),
            Tables\Columns\TextColumn::make('item_location.item.manufacturer')->label('Manufacturer')->sortable(),
            Tables\Columns\TextColumn::make('item_location.location.name')->label('Location')->sortable(),
            Tables\Columns\TextColumn::make('item_location.condition.name')->label('Condition')->sortable(),
            Tables\Columns\TextColumn::make('transaction_quantity')->label('Quantity')->alignRight()->sortable(),
            Tables\Columns\TextColumn::make('description')->label('Reference')->sortable()->searchable(),
        ];
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

                return $query->where('items.group', '=', $data['group']);
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
    public function getLocationTableFilter(): Tables\Filters\SelectFilter
    {
        return Tables\Filters\SelectFilter::make('location')
            ->options($this->locationOptions())
            ->attribute('location.id');
    }

    private function locationOptions(): array
    {
        return request()->user()->locations->pluck('name', 'id')->toArray();
    }

    /**
     * @throws Exception
     */
    public function getConditionTableFilter(): Tables\Filters\SelectFilter
    {
        return Tables\Filters\SelectFilter::make('condition')
            ->options($this->conditionOptions())
            ->attribute('condition.id');
    }

    private function conditionOptions(): array
    {
        return Condition::orderBy('name')->pluck('name', 'id')->toArray();
    }
}
