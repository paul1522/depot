<?php

namespace App\Filament\Resources\ItemResource\RelationManagers;

use App\Models\Document;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\Facades\Storage;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('path')
                    ->required()
                    ->disk('public')
                    ->directory('documents')
                    ->storeFileNamesIn('title')
                    ->acceptedFileTypes(['application/pdf', 'image/*']),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->before(function (Document $record) {
                    unlink(Storage::disk('public')->path($record->path));
                }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->before(function (\Illuminate\Database\Eloquent\Collection $records) {
                    foreach ($records as $record) {
                        unlink(Storage::disk('public')->path($record->path));
                    }
                }),
            ]);
    }
}
