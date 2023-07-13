<?php

namespace App\Filament\Pages;

use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use App\Models\Condition;
use App\Models\Item;
use App\Models\ItemLocation;
use App\Models\Location;
use Exception;
use Filament\Forms;
use Filament\Pages;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class InventoryStatusPage extends Pages\Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationLabel = 'Inventory Status';

    protected static ?string $title = 'Inventory Status Admin Report';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.inventory-status-page';

    public function getTableQuery(): Builder|Relation
    // Must be public for FilamentExportHeaderAction
    // Filament Tables hates ->join()
    {
        return ItemLocation::query()
            ->where('quantity', '<>', 0);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('item.description')->label('Description')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('item.sbt_item')->label('SBT Item Prefix')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('item.key')->label('Key')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('item.supplier_key')->label('Supplier key')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('item.group')->label('Group')->sortable(),
            Tables\Columns\TextColumn::make('item.manufacturer')->label('Manufacturer')->sortable(),
            Tables\Columns\TextColumn::make('location.name')->label('Location')->sortable(),
            Tables\Columns\TextColumn::make('condition.name')->label('Condition')->sortable(),
            Tables\Columns\TextColumn::make('quantity')->alignRight()->sortable(),
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
                ->fileNamePrefix('Depot Inventory Status Report'),
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 20, 50, 100];
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'item.description';
    }

    protected function getDefaultTableSortDirection(): string
    {
        return 'asc';
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
    protected function getTableFilters(): array
    {
        return [
            $this->getGroupTableFilter(),
            $this->getManufacturerTableFilter(),
            $this->getLocationTableFilter(),
            $this->getConditionTableFilter(),
        ];
    }

    /**
     * @throws Exception
     */
    public function getGroupTableFilter(): Tables\Filters\Filter
    {
        return Tables\Filters\Filter::make('group')
            ->form([
                Forms\Components\Select::make('group')
                    ->options($this->groupOptions())
                    ->placeholder('All'),
            ])
            ->query(function (Builder $query, array $data): Builder {
                if (! $data['group']) {
                    return $query;
                }

                return $query->whereIn('item_locations.item_id',
                    Item::whereGroup($data['group'])->pluck('id'));
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
                Forms\Components\Select::make('manufacturer')
                    ->options($this->manufacturerOptions())
                    ->placeholder('All'),
            ])
            ->query(function (Builder $query, array $data): Builder {
                if (! $data['manufacturer']) {
                    return $query;
                }

                return $query->whereIn('item_locations.item_id',
                    Item::whereManufacturer($data['manufacturer'])->pluck('id'));
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
        return Location::orderBy('name')->pluck('name', 'id')->toArray();
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
