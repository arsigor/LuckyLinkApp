<?php
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

test('create method shows the register page with necessary fields', function () {
    $response = $this->get(route('homepage'));

    $response->assertInertia(fn (Assert $page) =>
    $page->component('Auth/Register')
    ->has('formFields', 2)
    ->where('formFields.0.name', 'user_name')
    ->where('formFields.1.name', 'phone_number')
    );
});
test('store method registers a user and redirects to lucky page', function () {
    // Використовуємо БД для тестів
    $this->refreshDatabase();

    // Вхідні дані для реєстрації
    $data = [
        'user_name' => 'TestUser',
        'phone_number' => '+1234567890',
    ];

    // Виконуємо POST-запит
    $response = $this->post(route('register'), $data);

    // Переконуємося, що користувач створився в БД
    $this->assertDatabaseHas('users', [
        'user_name' => 'TestUser',
        'phone_number' => '+1234567890',
    ]);

    // Отримуємо створеного користувача
    $user = User::where('user_name', 'TestUser')->first();

    // Переконуємося, що згенеровано унікальне посилання
    $this->assertNotNull($user->unique_link);

    // Перевіряємо редирект на `lucky.index`
    $response->assertRedirect(route('lucky.index', ['token' => $user->unique_link]));

    // Перевіряємо, що є flash-повідомлення про успішну реєстрацію
    $response->assertSessionHas('success', 'You have registered and received a unique link.');
});

test('store method fails validation when user_name is missing', function () {
    $data = [
        'phone_number' => '+1234567890',
    ];

    $response = $this->post(route('register'), $data);

    $response->assertSessionHasErrors(['user_name']);
    $this->assertDatabaseMissing('users', ['phone_number' => '+1234567890']);
});

test('store method fails validation when phone_number is missing', function () {
    $data = [
        'user_name' => 'TestUser',
    ];

    $response = $this->post(route('register'), $data);

    $response->assertSessionHasErrors(['phone_number']);
    $this->assertDatabaseMissing('users', ['user_name' => 'TestUser']);
});
