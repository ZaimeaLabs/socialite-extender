<?php

namespace Zaimea\SocialiteExtender\Tests\Feature;

use Zaimea\SocialiteExtender\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Zaimea\SocialiteExtender\Models\SocialAccount;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;

class ConnectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a user to be authenticated
        User::factory()->create(['id' => 1, 'email' => 'test@example.com']);
        $this->actingAs(User::first());
    }

    public function test_callback_saves_social_account()
    {
        // Mock Socialite driver and returned user
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

        // Call controller route
        $this->get(route('socialite-extender.callback', ['provider' => 'github']))
            ->assertRedirect(); // redirect back to profile

        $this->assertDatabaseHas('social_accounts', [
            'provider' => 'github',
            'provider_user_id' => '12345',
            'nickname' => 'octocat',
        ]);
    }
}
