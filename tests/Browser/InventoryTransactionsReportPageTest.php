<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class InventoryTransactionsReportPageTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function testUserCanSeeInventoryTransactionsReport(): void
    {
        User::factory()->create();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit(route('reports.inventory.transactions'))
                ->assertSee('No records found');
        });
    }
}
