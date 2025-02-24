<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        Auth::logout();

        return Inertia::render('Auth/Register', [
            'formFields' => [
                ['name' => 'user_name', 'label' => 'User Name'],
                ['name' => 'phone_number', 'label' => 'Phone Number'],
            ],
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterUserRequest $request)
    {
        $user = User::create([
            'user_name' => $request->user_name,
            'phone_number' => $request->phone_number,
        ]);

        $user->generateUniqueLink();

        return redirect(route('lucky.index', ['token' => $user->unique_link]))
            ->with('success', 'You have registered and received a unique link.');
    }
}
