<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LandingPageTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function testGuestCanSeeLandingPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertPresent('@login-link')
                ->assertPresent('@register-link')
                ->assertNotPresent('@catalog-link');
        });
    }

    public function testUserCanSeeLandingPage(): void
    {
        User::factory()->create();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/')
                ->assertNotPresent('@login-link')
                ->assertNotPresent('@register-link')
                ->assertPresent('@catalog-link');
        });
    }

    public function testGuestCanClickLogin(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->click('@login-link')
                ->assertUrlIs(route('login'));
        });
    }

    public function testGuestCanClickRegister(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->click('@register-link')
                ->assertUrlIs(route('register'));
        });
    }

    public function testUserCanClickCatalog(): void
    {
        User::factory()->create();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/')
                ->click('@catalog-link')
                ->assertUrlIs(route('catalog.show'));
        });
    }
}
