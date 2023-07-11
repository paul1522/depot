<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CatalogPageTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function testUserCanSeeEmptyCatalog(): void
    {
        User::factory()->create();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit(route('catalog.show'))
                ->assertSee('No records found');
        });
    }
}
