<?php

namespace App\Filament\Resources\ItemResource\RelationManagers;

use App\Models\Item;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

class BillOfMaterialsRelationManager extends RelationManager
{
    protected static string $relationship = 'bill_of_materials';

    protected static ?string $recordTitleAttribute = 'item';

    protected static ?string $label = 'BOM item';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('item_id')
                    ->relationship('item', 'sbt_item')
                    ->searchable()
                    ->reactive()
                    ->required(),
                Forms\Components\Placeholder::make('description')
                    ->content(function (\Closure $get) {
                        return Item::find($get('item_id'))->description ?? '';
                    }),
                Forms\Components\TextInput::make('option_group')
                    ->maxLength(255),
                //                Forms\Components\TextInput::make('min_qty')->default(0)
                //                    ->integer()
                //                    ->minValue(0)
                //                    ->required(),
                //                Forms\Components\TextInput::make('max_qty')->default(1)
                //                    ->integer()
                //                    ->minValue(1)
                //                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('option_group')->sortable(),
                Tables\Columns\TextColumn::make('item.sbt_item')->label('SBT Item')->sortable(),
                Tables\Columns\TextColumn::make('item.description')->label('Description')->sortable(),
                //                Tables\Columns\TextColumn::make('min_qty')->sortable(),
                //                Tables\Columns\TextColumn::make('max_qty')->sortable(),
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
