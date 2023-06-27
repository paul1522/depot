<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class InventoryTransactionsPage extends Page
{
    protected static ?string $navigationLabel = 'Inventory Transactions';

    protected static ?string $title = 'Inventory Transactions Report';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.inventory-transactions-page';
}
