<?php

namespace App\Http\Controllers;

use App\Services\LuckyGameService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class LuckyPageController extends Controller
{
    /**
     * Display the LuckyPage with the user's recent game history.
     *
     * @param UserService $userService
     * @param string $token
     * @return InertiaResponse
     */
    public function index(UserService $userService, string $token): InertiaResponse
    {
        $userService->validateUserAndLogin($token);

        return Inertia::render('LuckyPage', [
            'token' => $token,
        ]);
    }

    /**
     * Regenerate a unique link for the user.
     *
     * @param UserService $userService
     * @param string $token
     * @return RedirectResponse
     */
    public function regenerateLink(UserService $userService, string $token): RedirectResponse
    {
        $user = $userService->validateUserAndLogin($token);

        $user->generateUniqueLink();

        return Redirect::route('lucky.index', ['token' => $user->unique_link])
            ->with('success', 'A new unique link has been generated');
    }

    /**
     * Deactivate the user's unique link and log them out.
     *
     * @param UserService $userService
     * @param string $token
     * @return RedirectResponse
     */
    public function deactivateLink(UserService $userService, string $token): RedirectResponse
    {
        $user = $userService->validateUserAndLogin($token);

        $user->update(['unique_link' => null, 'link_expires_at' => null]);

        Auth::logout();

        return Redirect::route('homepage');
    }

    /**
     * Play the Lucky game for the user and return the result.
     *
     * @param LuckyGameService $luckyGameService
     * @param string $token
     * @return JsonResponse
     */
    public function playLuckyGame(LuckyGameService $luckyGameService, string $token): JsonResponse
    {
        return Response::json($luckyGameService->play($token));
    }

    /**
     * Get the user's recent 3 game history.
     *
     * @param UserService $userService
     * @param string $token
     * @return JsonResponse
     */
    public function getHistory(UserService $userService, string $token): JsonResponse
    {
        $user = $userService->validateUserAndLogin($token);

        return Response::json(['history' => $user->luckyResults()->latest()->take(3)->get()]);
    }
}
