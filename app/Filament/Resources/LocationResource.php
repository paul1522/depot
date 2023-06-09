<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocationResource\Pages;
use App\Filament\Resources\LocationResource\RelationManagers;
use App\Models\Location;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\TextInput::make('address1')->required()->maxLength(255),
                Forms\Components\TextInput::make('address2')->maxLength(255),
                Forms\Components\TextInput::make('city')->required()->maxLength(255),
                Forms\Components\TextInput::make('state')->required()->maxLength(255),
                Forms\Components\TextInput::make('zip')->required()->maxLength(255),
                Forms\Components\TextInput::make('sbt_loctid')->label('SBT location')->required()->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('address1')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('address2')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('city')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('state')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('zip')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('sbt_loctid')->label('SBT location')->sortable()->searchable(),
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
            RelationManagers\UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLocations::route('/'),
            'create' => Pages\CreateLocation::route('/create'),
            'edit' => Pages\EditLocation::route('/{record}/edit'),
        ];
    }
}
