<?php

namespace App\Http\Controllers\Api;

use App\Models\ShopOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Middleware\BlockIpAfterFailedAttempts;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $ip = $request->ip();

        if ($this->hasTooManyLoginAttempts($request)) {
            BlockIpAfterFailedAttempts::incrementAttempts($ip);
            return $this->sendLockoutResponse($request);
        }


        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            BlockIpAfterFailedAttempts::resetAttempts($ip);
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        } else {
            BlockIpAfterFailedAttempts::incrementAttempts($ip);
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    protected function hasTooManyLoginAttempts(Request $request)
    {
        $ip = $request->ip();
        return Cache::get('login_attempts_' . $ip, 0) >= BlockIpAfterFailedAttempts::MAX_ATTEMPTS;
    }

    protected function sendLockoutResponse(Request $request)
    {
        $ip = $request->ip();
        $seconds = Cache::get('login_attempts_' . $ip . '_block_time') - now()->timestamp;
        return response()->json(['message' => 'Too many attempts. Try again in ' . $seconds . ' seconds.'], 429);
    }

    public function completeOrders(Request $request)
    {
        $orderIds = $request->input('order_ids');

        ShopOrder::whereIn('id', $orderIds)
            ->update(['order_state' => 'Skompletowane']);

        return response()->json(['message' => 'Orders updated successfully']);
    }
}
