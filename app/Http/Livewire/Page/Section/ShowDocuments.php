<?php

namespace App\Http\Livewire\Page\Section;

use App\Models\BillOfMaterials;
use App\Models\Document;
use App\Models\Item;
use App\Models\ItemLocation;
use App\Models\Location;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class ShowDocuments extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public Item $item;

    public function render()
    {
        return view('livewire.page.section.documents');
    }

    protected function getTableQuery(): Builder
    {
        return Document::query()
            ->selectRaw('hash, min(id) as id, min(title) as title, min(path) as path')
            ->whereItemId($this->item->id)
            ->orWhereIn('item_id',
                BillOfMaterials::whereMasterItemId($this->item->id)->pluck('item_id'))
            ->groupBy('hash');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('view')->icon('heroicon-s-eye')->url(function (Document $record) {
                return Storage::disk('public')->url($record->path);
            })->openUrlInNewTab(),
        ];
    }
}
