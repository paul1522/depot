<?php

namespace App\Http\Livewire;

use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use App\Models\Condition;
use App\Models\Item;
use App\Models\Location;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class InventoryTransactionsReport extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public function render()
    {
        return view('livewire.inventory-transactions-report')
            ->layout('layouts.app', [
                'drawer_open' => true,
            ]);
    }

    public function getTableQuery(): Builder|Relation
    // Must be public for FilamentExportHeaderAction
    {
        return Transaction::query()
            ->selectRaw('transactions.id, transactions.date, transactions.quantity, transactions.description, transactions.item_location_id, item_locations.location_id')
            ->join('item_locations', 'item_locations.id', '=', 'transactions.item_location_id')
            ->join('locations', 'locations.id', '=', 'item_locations.location_id')
            ->join('location_user', 'location_user.location_id', '=', 'locations.id')
            ->where('location_user.user_id', '=', request()->user()->id);
    }

    protected function getTableColumns(): array
    {
        $columns = [
            Tables\Columns\TextColumn::make('date')->date(),
            Tables\Columns\TextColumn::make('item_location.item.description')->label('Description')->searchable(),
            Tables\Columns\TextColumn::make('item_location.item.key')->label('Key')->searchable(),
            Tables\Columns\TextColumn::make('item_location.item.supplier_key')->label('Supplier key')->searchable(),
            Tables\Columns\TextColumn::make('item_location.item.group')->label('Group'),
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
                ->fileNamePrefix('Depot Inventory Transactions Report'),
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

                    return $query->whereRaw('`item_id` in (select id from items where `group` = \''.$data['group'].'\')');
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

                    return $query->whereRaw('`item_id` in (select id from items where `manufacturer` = \''.$data['manufacturer'].'\')');
                })
                ->indicateUsing(function (array $data): ?string {
                    return $data['manufacturer'] ? 'Manufacturer: '.$data['manufacturer'] : null;
                }),
            Tables\Filters\SelectFilter::make('location')
                ->options($this->locationOptions())
                ->attribute('item_location.location_id'),
            Tables\Filters\SelectFilter::make('condition')
                ->options($this->conditionOptions())
                ->attribute('item_location.condition_id'),
        ];
    }

    private function locationOptions(): array
    {
        return Location::whereIn('id', $this->locationIds())->orderBy('name')->pluck('name', 'id')->toArray();
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

    protected function locationIds(): array
    {
        return DB::table('location_user')
            ->select('location_id')
            ->where('location_user.user_id', '=', request()->user()->id)
            ->pluck('location_id')
            ->toArray();
    }
}
