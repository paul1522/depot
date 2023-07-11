<?php

namespace App\Http\Livewire;

use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use App\Models\Condition;
use App\Models\Item;
use App\Models\ItemLocation;
use App\Models\Location;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class InventoryStatusReport extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public function render()
    {
        return view('livewire.inventory-status-report');
    }

    public function getTableQuery(): Builder|Relation
    // Must be public for FilamentExportHeaderAction
    {
        return ItemLocation::query()
            ->select(['item_locations.id', 'item_locations.item_id', 'item_locations.location_id', 'item_locations.condition_id', 'item_locations.quantity'])
            ->join('items', 'items.id', '=', 'item_locations.item_id')
            ->join('locations', 'locations.id', '=', 'item_locations.location_id')
            ->join('conditions', 'conditions.id', '=', 'item_locations.condition_id')
            ->join('location_user', 'location_user.location_id', '=', 'locations.id')
            ->where('quantity', '>', 0)
            ->where('location_user.user_id', '=', request()->user()->id)
            ->orderBy('items.description')
            ->orderBy('locations.name')
            ->orderBy('conditions.name');
    }

    protected function getTableColumns(): array
    {
        $columns = [
            Tables\Columns\TextColumn::make('item.description')->label('Description')->searchable(),
            Tables\Columns\TextColumn::make('item.key')->label('Key')->searchable(),
            Tables\Columns\TextColumn::make('item.supplier_key')->label('Supplier key')->searchable(),
            Tables\Columns\TextColumn::make('item.group')->label('Group'),
            Tables\Columns\TextColumn::make('item.manufacturer')->label('Manufacturer'),
            Tables\Columns\TextColumn::make('location.name')->label('Location'),
            Tables\Columns\TextColumn::make('condition.name')->label('Condition'),
            Tables\Columns\TextColumn::make('quantity')->alignRight(),
        ];

        return $columns;
    }

    protected function getTableHeaderActions(): array
    {
        return [
            FilamentExportHeaderAction::make('Export')
                ->disableAdditionalColumns()
                ->disableFilterColumns()
                ->fileNamePrefix('Depot Inventory Status Report'),
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
                ->attribute('location.id'),
            Tables\Filters\SelectFilter::make('condition')
                ->options($this->conditionOptions())
                ->attribute('condition.id'),
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
            ->where('user_id', '=', request()->user()->id)
            ->pluck('location_id')
            ->toArray();
    }
}
