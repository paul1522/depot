<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Pages;
use App\Filament\Resources\ItemResource\RelationManagers;
use App\Filament\Resources\ItemResource\RelationManagers\BillOfMaterialsRelationManager;
use App\Filament\Resources\ItemResource\RelationManagers\DocumentsRelationManager;
use App\Models\Item;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')->required()->maxLength(255),
                Forms\Components\TextInput::make('supplier_key')->maxLength(255),
                Forms\Components\TextInput::make('description')->required()->maxLength(255),
                Forms\Components\TextInput::make('group')->maxLength(255),
                Forms\Components\TextInput::make('manufacturer')->maxLength(255),
                Forms\Components\TextInput::make('sbt_item')->required()->maxLength(255),
                Forms\Components\FileUpload::make('image_path')
                    ->label('Upload product image')
                    ->disk('public')
                    ->directory('images')
                    ->storeFileNamesIn('image_name')
                    ->image(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('supplier_key')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('description')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('group')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('manufacturer')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('sbt_item')->sortable()->searchable(),
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
                Tables\Actions\DeleteBulkAction::make(),
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
