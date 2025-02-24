<?php

namespace App\Services;

use App\Models\LuckyResult;

class LuckyGameService
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function play(string $token): array
    {
        $user = $this->userService->validateUserAndLogin($token);

        $randomNumber = rand(1, 1000);
        $isWin = $randomNumber % 2 === 0;
        $winAmount = $this->calculateWinAmount($randomNumber, $isWin);

        LuckyResult::create([
            'user_id' => $user->id,
            'random_number' => $randomNumber,
            'result' => $isWin ? 'Win' : 'Lose',
            'win_amount' => $winAmount,
        ]);

        return [
            'gameResult' => [
                'random_number' => $randomNumber,
                'result' => $isWin ? 'Win' : 'Lose',
                'win_amount' => $winAmount,
            ],
        ];
    }

    private function calculateWinAmount(int $randomNumber, bool $isWin): float
    {
        if (!$isWin) {
            return 0;
        }

        if ($randomNumber > 900) {
            return $randomNumber * 0.7;
        } elseif ($randomNumber > 600) {
            return $randomNumber * 0.5;
        } elseif ($randomNumber > 300) {
            return $randomNumber * 0.3;
        } else {
            return $randomNumber * 0.1;
        }
    }
}
