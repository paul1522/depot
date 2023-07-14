<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LandingPageTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function testSiteTitleIsCorrect(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertTitle(config('app.name'));
        });
    }

    public function testGuestIsRedirectedToRegistrationPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertUrlIs(route('register'));
        });
    }

    public function testUserIsRedirectedToCatalogPage(): void
    {
        User::factory()->create();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/')
                ->assertUrlIs(route('catalog.show'));
        });
    }
}
