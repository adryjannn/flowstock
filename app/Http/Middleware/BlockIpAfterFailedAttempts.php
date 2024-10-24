<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;

class BlockIpAfterFailedAttempts
{
    const MAX_ATTEMPTS = 5;
    const BLOCK_TIME = 900; // 15 minutes

    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();
        $key = 'login_attempts_' . $ip;
        $attempts = Cache::get($key, 0);

        if ($attempts >= self::MAX_ATTEMPTS) {
            $remaining = Cache::get($key . '_block_time') - now()->timestamp;
            if ($remaining > 0) {
                return response()->json(['message' => 'Too many attempts. Try again later.'], 429);
            } else {
                Cache::forget($key);
                Cache::forget($key . '_block_time');
            }
        }

        return $next($request);
    }

    public static function incrementAttempts($ip)
    {
        $key = 'login_attempts_' . $ip;
        $attempts = Cache::increment($key);

        if ($attempts >= self::MAX_ATTEMPTS) {
            Cache::put($key . '_block_time', now()->addSeconds(self::BLOCK_TIME)->timestamp, self::BLOCK_TIME);
        }
    }

    public static function resetAttempts($ip)
    {
        Cache::forget('login_attempts_' . $ip);
        Cache::forget('login_attempts_' . $ip . '_block_time');
    }
}
