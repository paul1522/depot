<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class InventoryStatusReportPageTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function testUserCanSeeEmptyInventoryStatusReport(): void
    {
        User::factory()->create();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit(route('reports.inventory.status'))
                ->assertSee('No records found');
        });
    }
}
