<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class OrdersPageTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function testUserCanSeeOrders(): void
    {
        User::factory()->create();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit(route('orders.show'))
                ->assertSee('No orders found');
        });
    }
}
