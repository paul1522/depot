<?php

namespace App\Filament\Pages;

use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use App\Models\Condition;
use App\Models\Item;
use App\Models\ItemLocation;
use App\Models\Location;
use App\Models\Transaction;
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
            ->select(['transactions.id', 'transactions.date', 'transactions.item_location_id', 'transactions.quantity', 'transactions.description'])
            ->join('item_locations', 'item_locations.id', '=', 'transactions.item_location_id')
            ->orderBy('transactions.sbt_ttranno');
    }

    protected function getTableColumns(): array
    {
        $columns = [
            Tables\Columns\TextColumn::make('date')->date(),
            Tables\Columns\TextColumn::make('item_location.item.description')->label('Charter description')->searchable(),
            Tables\Columns\TextColumn::make('item_location.item.sbt_item')->label('SBT item prefix')->searchable(),
            Tables\Columns\TextColumn::make('item_location.item.key')->label('Charter key')->searchable(),
            Tables\Columns\TextColumn::make('item_location.item.supplier_key')->label('Charter supplier key')->searchable(),
            Tables\Columns\TextColumn::make('item_location.item.group')->label('Charter group'),
            Tables\Columns\TextColumn::make('item_location.item.manufacturer')->label('Manufacturer'),
            Tables\Columns\TextColumn::make('item_location.location.name')->label('Location'),
            Tables\Columns\TextColumn::make('item_location.condition.name')->label('Condition'),
            Tables\Columns\TextColumn::make('quantity')->label('Qty'),
            Tables\Columns\TextColumn::make('description')->label('Reference')->searchable(),
        ];

        return $columns;
    }

    protected function getTableHeaderActions(): array
    {
        return [
            FilamentExportHeaderAction::make('Export')
                ->disableAdditionalColumns()
                ->disableFilterColumns()
                ->fileNamePrefix('Depot Inventory Transactions Admin Report'),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\Filter::make('group')
                ->form([
                    Forms\Components\Select::make('group')->options($this->groupOptions())->placeholder('All'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    if (! $data['group']) {
                        return $query;
                    }

                    return $query->whereIn('item_locations.item_id',
                        Item::where(
                            'group',
                            '=',
                            $data['group']
                        )->pluck('id')
                    );
                })
                ->indicateUsing(function (array $data): ?string {
                    return $data['group'] ? 'Group: '.$data['group'] : null;
                }),
            Tables\Filters\Filter::make('manufacturer')
                ->form([
                    Forms\Components\Select::make('manufacturer')->options($this->manufacturerOptions())->placeholder('All'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    if (! $data['manufacturer']) {
                        return $query;
                    }

                    return $query->whereIn('item_locations.item_id',
                        Item::where(
                            'manufacturer',
                            '=',
                            $data['manufacturer']
                        )->pluck('id')
                    );
                })
                ->indicateUsing(function (array $data): ?string {
                    return $data['manufacturer'] ? 'Manufacturer: '.$data['manufacturer'] : null;
                }),
            Tables\Filters\SelectFilter::make('location')
                ->options($this->locationOptions())
                ->attribute('location_id'),
            Tables\Filters\SelectFilter::make('condition')
                ->options($this->conditionOptions())
                ->attribute('condition_id'),
        ];
    }

    private function locationOptions(): array
    {
        return Location::whereRaw(1)->orderBy('name')->pluck('name', 'id')->toArray();
    }

    private function conditionOptions(): array
    {
        return Condition::whereRaw(1)->orderBy('name')->pluck('name', 'id')->toArray();
    }

    private function groupOptions(): array
    {
        return Item::distinct()
            ->pluck('group', 'group')
            ->toArray();
    }

    private function manufacturerOptions(): array
    {
        return Item::distinct()
            ->pluck('manufacturer', 'manufacturer')
            ->toArray();
    }
}
