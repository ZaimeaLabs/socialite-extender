<?php

namespace Zaimea\SocialiteExtender\Tests\Feature;

use Zaimea\SocialiteExtender\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ConnectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Migrations are loaded via TestCase::defineDatabaseMigrations()
        // Create a user in the database (no artisan migrate here)
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            // plain password for test; hashed
            'password' => Hash::make('password'),
        ]);

        $this->actingAs(User::first());
    }

    public function test_callback_saves_social_account()
    {
        // Mock Socialite provider user
        $socialiteUser = new SocialiteUser();
        $socialiteUser->id = '12345';
        $socialiteUser->nickname = 'octocat';
        $socialiteUser->name = 'The Cat';
        $socialiteUser->token = 'token-abc';
        $socialiteUser->refreshToken = 'refresh-123';
        $socialiteUser->expiresIn = 3600;
        $socialiteUser->avatar = 'https://avatars.githubusercontent.com/u/12345';

        $driverMock = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $driverMock->shouldReceive('stateless')->andReturnSelf();
        $driverMock->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('github')->andReturn($driverMock);

        $this->get(route('socialite-extender.callback', ['provider' => 'github']))
             ->assertRedirect();

        $this->assertDatabaseHas('social_accounts', [
            'provider' => 'github',
            'provider_user_id' => '12345',
            'nickname' => 'octocat',
        ]);
    }
}
