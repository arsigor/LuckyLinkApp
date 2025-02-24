<?php

use App\Models\LuckyResult;
use App\Models\User;
use App\Services\LuckyGameService;
use Illuminate\Support\Facades\Auth;
use Inertia\Testing\AssertableInertia as Assert;

test('index method shows the lucky page with token', function () {
    $user = User::factory()->create();
    $token = $user->unique_link;

    Auth::login($user);

    $response = $this->get(route('lucky.index', ['token' => $token]));

    $response->assertInertia(fn (Assert $page) =>
    $page->component('LuckyPage')
        ->where('token', $token)
    );
});

test('regenerateLink method generates a new unique link', function () {
    $user = User::factory()->create();
    $oldToken = $user->unique_link;

    Auth::login($user);

    $response = $this->post(route('lucky.generate-link', ['token' => $oldToken]));

    $user->refresh();
    $this->assertNotEquals($oldToken, $user->unique_link);

    $response->assertRedirect(route('lucky.index', ['token' => $user->unique_link]));
    $response->assertSessionHas('success', 'A new unique link has been generated');
});

test('deactivateLink method deactivates link and logs user out', function () {
    $user = User::factory()->create();

    Auth::login($user);

    $response = $this->post(route('lucky.deactivate-link', ['token' => $user->unique_link]));

    $user->refresh();
    $this->assertNull($user->unique_link);
    $this->assertNull($user->link_expires_at);

    $response->assertRedirect(route('homepage'));
    $this->assertGuest(); // Перевірка, що користувач вийшов
});

test('playLuckyGame method plays the game and returns result', function () {
    $user = User::factory()->create();

    Auth::login($user);

    $luckyGameServiceMock = \Mockery::mock(LuckyGameService::class);
    $luckyGameServiceMock->shouldReceive('play')->with($user->unique_link)->andReturn(['result' => 'win']);

    app()->instance(LuckyGameService::class, $luckyGameServiceMock);

    $response = $this->post(route('lucky.play', ['token' => $user->unique_link]));

    $response->assertJson(['result' => 'win']);
});

test('getHistory method returns recent game history', function () {
    $user = User::factory()->create();
    $token = $user->unique_link;

    Auth::login($user);

    LuckyResult::create([
        'user_id' => $user->id,
        'result' => 'win',
        'random_number' => rand(1, 100),
        'win_amount' => rand(1, 1000),
    ]);

    $response = $this->post(route('lucky.history', ['token' => $token]));

    $response->assertJson(function ($json) {
        $json->has('history')
            ->etc();
    });
});
