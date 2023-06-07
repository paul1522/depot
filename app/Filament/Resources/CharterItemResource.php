<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CharterItemResource\Pages;
use App\Models\CharterItem;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class CharterItemResource extends Resource
{
    protected static ?string $model = CharterItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')->required()->maxLength(255),
                Forms\Components\TextInput::make('supplier_key')->required()->maxLength(255),
                Forms\Components\TextInput::make('description')->required()->maxLength(255),
                Forms\Components\TextInput::make('group')->required()->maxLength(255),
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
//                Tables\Columns\TextColumn::make('created_at')->dateTime(),
//                Tables\Columns\TextColumn::make('updated_at')->dateTime(),
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
            'index' => Pages\ListCharterItems::route('/'),
            'create' => Pages\CreateCharterItem::route('/create'),
            'edit' => Pages\EditCharterItem::route('/{record}/edit'),
        ];
    }
}
