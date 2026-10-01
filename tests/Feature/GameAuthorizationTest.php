<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GameAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_only_view_the_game_overview(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole(Role::create(['name' => 'klant', 'guard_name' => 'web']));

        $this->actingAs($customer)->get('/games')->assertOk();
        $this->actingAs($customer)->get('/games/create')->assertForbidden();
        $this->actingAs($customer)->get('/games/show/1')->assertForbidden();
        $this->actingAs($customer)->post('/games/store')->assertForbidden();
        $this->actingAs($customer)->post('/games/update/1')->assertForbidden();
        $this->actingAs($customer)->delete('/games/delete/1')->assertForbidden();
    }

    public function test_admin_can_view_and_manage_games(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::create(['name' => 'admin', 'guard_name' => 'web']));
        $game = Game::create([
            'game_name' => 'Test Game',
            'platform' => 'PC',
            'genre' => 'Adventure',
            'rating' => 8.0,
        ]);

        $this->actingAs($admin)->get('/games')->assertOk();
        $this->actingAs($admin)->get('/games/create')->assertOk();
        $this->actingAs($admin)->get('/games/show/'.$game->id)->assertOk();
        $this->actingAs($admin)->get('/games/edit/'.$game->id)->assertOk();
        $this->actingAs($admin)->post('/games/store', [
            'game_name' => 'New Game',
            'platform' => 'PC',
            'genre' => 'RPG',
            'rating' => 7.5,
        ])->assertRedirect('/games');
        $this->actingAs($admin)->post('/games/update/'.$game->id, [
            'game_name' => 'Updated Game',
            'platform' => 'PC',
            'genre' => 'Adventure',
            'rating' => 9.0,
        ])->assertRedirect('/games');
        $this->actingAs($admin)->delete('/games/delete/'.$game->id)
            ->assertRedirect('/games');

        $this->assertDatabaseMissing('games', ['id' => $game->id]);
    }
}