<?php

namespace Tests\Browser;

use App\Models\CartedItem;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CartPageTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function testUserCanSeeEmptyCart(): void
    {
        User::factory()->create();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit(route('cart.show'))
                ->assertSee('Your cart is empty');
        });
    }
}
