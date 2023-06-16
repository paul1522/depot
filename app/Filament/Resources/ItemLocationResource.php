<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemLocationResource\Pages;
use App\Models\ItemLocation;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class ItemLocationResource extends Resource
{
    protected static ?string $model = ItemLocation::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('location_id')
                    ->relationship('location', 'sbt_loctid')->required(),
                Forms\Components\Select::make('item_id')
                    ->relationship('item', 'sbt_item')->required(),
                Forms\Components\TextInput::make('quantity')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('location.sbt_loctid')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('item.sbt_item')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('quantity')->sortable(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItemLocations::route('/'),
            'create' => Pages\CreateItemLocation::route('/create'),
            'edit' => Pages\EditItemLocation::route('/{record}/edit'),
        ];
    }
}
