<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemLocationResource\Pages;
use App\Filament\Resources\ItemLocationResource\RelationManagers;
use App\Models\ItemLocation;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemLocationResource extends Resource
{
    protected static ?string $model = ItemLocation::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('item_id')
                    ->relationship('item', 'id')
                    ->required(),
                Forms\Components\Select::make('location_id')
                    ->relationship('location', 'name')
                    ->required(),
                Forms\Components\Select::make('condition_id')
                    ->relationship('condition', 'name'),
                Forms\Components\TextInput::make('quantity')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('item.id'),
                Tables\Columns\TextColumn::make('item.description'),
                Tables\Columns\TextColumn::make('item.sbt_item'),
                Tables\Columns\TextColumn::make('location.name'),
                Tables\Columns\TextColumn::make('condition.name'),
                Tables\Columns\TextColumn::make('quantity'),
                // Tables\Columns\TextColumn::make('created_at')->dateTime(),
                // Tables\Columns\TextColumn::make('updated_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
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
            // 'view' => Pages\ViewItemLocation::route('/{record}'),
            // 'edit' => Pages\EditItemLocation::route('/{record}/edit'),
        ];
    }
}
