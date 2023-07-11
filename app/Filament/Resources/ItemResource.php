<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Pages;
use App\Filament\Resources\ItemResource\RelationManagers\BillOfMaterialsRelationManager;
use App\Filament\Resources\ItemResource\RelationManagers\DocumentsRelationManager;
use App\Models\Item;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')->label('Charter key')->required()->maxLength(255),
                Forms\Components\TextInput::make('supplier_key')->label('Charter supplier key')->maxLength(255),
                Forms\Components\TextInput::make('description')->label('Charter description')->required()->maxLength(255),
                Forms\Components\TextInput::make('group')->label('Charter group')->maxLength(255),
                Forms\Components\TextInput::make('manufacturer')->label('Charter manufacturer')->maxLength(255),
                Forms\Components\TextInput::make('sbt_item')->label('SBT item prefix')->required()->maxLength(255),
                Forms\Components\FileUpload::make('image_path')
                    ->label('Upload product image')
                    ->disk('public')
                    ->directory('images')
                    ->storeFileNamesIn('image_name')
                    ->image(),
                //                Forms\Components\Placeholder::make('image')->label('Image')->content(function (Item $record) {
                //                    // return $record->image_path;
                //                    return new HtmlString("<img class='object-contain' src='".Storage::url($record->image_path)."'/>");
                //                }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')->label('Charter key')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('supplier_key')->label('Charter supplier key')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('description')->label('Charter description')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('group')->label('Charter group')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('manufacturer')->label('Charter manufacturer')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('sbt_item')->label('SBT item prefix')->sortable()->searchable(),
                // Tables\Columns\TextColumn::make('created_at')->dateTime(),
                // Tables\Columns\TextColumn::make('updated_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            BillOfMaterialsRelationManager::class,
            DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }
}
