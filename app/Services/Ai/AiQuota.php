<?php

namespace App\Services\Ai;

use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;

/**
 * Daily limit on assistant requests. Logged-in users are counted per account and guests per browser session,
 * with a loose per-IP ceiling against scripts. Admins are not limited. Only the counters live in the cache
 * (the IP as a hash) and they expire at midnight, so nothing about the visitor is kept.
 */
class AiQuota
{
    public function __construct(private RateLimiter $limiter) {}

    /**
     * @return array{limit: int|null, remaining: int|null, isGuest: bool}
     */
    public function status(Request $request): array
    {
        return [
            'limit' => $this->limit($request),
            'remaining' => $this->remaining($request),
            'isGuest' => $request->user() === null,
        ];
    }

    public function hasRemaining(Request $request): bool
    {
        return $this->remaining($request) !== 0;
    }

    /**
     * Call once the assistant has answered, so a failed call does not use up the visitor's allowance.
     */
    public function consume(Request $request): void
    {
        if ($this->limit($request) === null) {
            return;
        }

        foreach (array_keys($this->counters($request)) as $key) {
            $this->limiter->hit($key, $this->secondsUntilReset());
        }
    }

    public function exhaustedMessage(Request $request): string
    {
        $limit = $this->limit($request);

        if ($request->user() !== null) {
            return "Jatah asisten hari ini habis ({$limit} permintaan per hari). Coba lagi besok.";
        }

        $userLimit = config('cariprint.ai.quota.user');

        return "Jatah asisten hari ini habis ({$limit} permintaan per hari). Masuk dengan Google untuk mendapat {$userLimit} per hari, atau coba lagi besok.";
    }

    /**
     * Null means unlimited.
     */
    private function limit(Request $request): ?int
    {
        $user = $request->user();

        if ($user?->isAdmin()) {
            return null;
        }

        return config($user === null ? 'cariprint.ai.quota.guest' : 'cariprint.ai.quota.user');
    }

    /**
     * Null means unlimited.
     */
    private function remaining(Request $request): ?int
    {
        if ($this->limit($request) === null) {
            return null;
        }

        $remaining = PHP_INT_MAX;

        foreach ($this->counters($request) as $key => $max) {
            $remaining = min($remaining, $this->limiter->remaining($key, $max));
        }

        return $remaining;
    }

    /**
     * Counter key => the most it may reach today.
     *
     * @return array<string, int>
     */
    private function counters(Request $request): array
    {
        $user = $request->user();

        if ($user !== null) {
            return ["ai-quota:user:{$user->id}" => $this->limit($request)];
        }

        return [
            "ai-quota:session:{$request->session()->getId()}" => $this->limit($request),
            'ai-quota:ip:'.sha1((string) $request->ip()) => config('cariprint.ai.quota.ip_ceiling'),
        ];
    }

    private function secondsUntilReset(): int
    {
        $now = now(config('cariprint.ai.reset_timezone'));

        return max(1, (int) $now->diffInSeconds($now->endOfDay(), true));
    }
}
