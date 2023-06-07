<?php

namespace App\Filament\Resources\BillOfMaterialsHeaderResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'details';

    protected static ?string $recordTitleAttribute = 'sbt_item';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('item_id')
                    ->relationship('item', 'sbt_item')->required(),
                Forms\Components\TextInput::make('option_group')->maxLength(255),
                Forms\Components\TextInput::make('min_qty')->required()->numeric()->minValue(0)->default(1),
                Forms\Components\TextInput::make('max_qty')->required()->numeric()->minValue(0)->default(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('option_group'),
                Tables\Columns\TextColumn::make('item.sbt_item'),
                Tables\Columns\TextColumn::make('item.description'),
                Tables\Columns\TextColumn::make('min_qty'),
                Tables\Columns\TextColumn::make('max_qty'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
