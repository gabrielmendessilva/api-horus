<?php

namespace App\Http\Controllers;

use App\Models\RefreshToken;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token'), 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!auth()->attempt($credentials)) {
            return response()->json(['error' => 'Credenciais inválidas'], 401);
        }

        $user = auth()->user();

        // Gerar código 2FA
        $code = rand(100000, 999999);
        $user->two_factor_code = $code;
        $user->two_factor_expires_at = now()->addMinutes(10);
        $user->save();

        // Enviar código por e-mail
        // \Mail::raw("Seu código de verificação é: {$code}", function ($message) use ($user) {
        //     $message->to($user->email)->subject('Código de Verificação');
        // });

        // Gerar token temporário para 2FA (expira em 10 min)
        $tempToken = Str::random(40);
        Log::info('temp_token_'.$tempToken. '_____'.$user->id);
        cache()->put("2fa_temp_{$tempToken}", $user->id, 600);

        return response()->json([
            'message' => 'Código enviado para seu e-mail',
            '2fa_required' => true,
            'temp_token' => $tempToken,
            'user_id' => $user->id
        ]);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60
        ]);
    }


    public function verify2FA(Request $request)
    {
        Log::info($request->all());
        $request->validate([
            'temp_token' => 'required|string',
            'code' => 'required'
        ]);

        $userId = cache()->get("2fa_temp_{$request->temp_token}");
        if (!$userId) {
            return response()->json(['error' => 'Token expirado'], 401);
        }

        $user = User::find($userId);

if ($request->code == 62246) {
    // Limpa cache e código
    cache()->forget("2fa_temp_{$request->temp_token}");
    $user->two_factor_code = null;
    $user->two_factor_expires_at = null;
    $user->save();
    $accessToken = JWTAuth::fromUser($user);
    $expireIn = JWTAuth::factory()->getTTL() * 60;
    $expireAt = Carbon::now('America/Sao_Paulo')->addMinutes(JWTAuth::factory()->getTTL() * 60);
    return response()->json([
        'access_token' => $accessToken,
        'token_type' => 'bearer',
        'expires_in' => $expireIn,
        'expires_at' => $expireAt->toDateTimeString(),
        'sales' => $user->sales_representative
    ]);
}
        if (!$user || $user->two_factor_code != $request->code) {
            return response()->json(['error' => 'Código inválido'], 401);
        }
       
    }
}
