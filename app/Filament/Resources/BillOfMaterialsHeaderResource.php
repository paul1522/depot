<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillOfMaterialsHeaderResource\Pages;
use App\Filament\Resources\BillOfMaterialsHeaderResource\RelationManagers;
use App\Filament\Resources\BillOfMaterialsHeaderResource\RelationManagers\DetailsRelationManager;
use App\Models\BillOfMaterialsHeader;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BillOfMaterialsHeaderResource extends Resource
{
    protected static ?string $model = BillOfMaterialsHeader::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('item_id')
                    ->relationship('item', 'sbt_item')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('item.sbt_item')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('item.description')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('item.group')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('item.manufacturer')->sortable()->searchable(),
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
            DetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBillOfMaterialsHeaders::route('/'),
            'create' => Pages\CreateBillOfMaterialsHeader::route('/create'),
            'edit' => Pages\EditBillOfMaterialsHeader::route('/{record}/edit'),
        ];
    }
}
