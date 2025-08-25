<?php

namespace App\Http\Controllers;

use App\Models\RefreshToken;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;

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

        if (!$user = User::where('email', $credentials['email'])->first()) {
            return response()->json(['error' => 'Usuário não encontrado'], 404);
        }

        if (!auth()->attempt($credentials)) {
            return response()->json(['error' => 'Credenciais inválidas'], 401);
        }

        // Gerar código 2FA (ex: 6 dígitos aleatórios)
        $code = rand(100000, 999999);

        $user->two_factor_code = $code;
        $user->two_factor_expires_at = now()->addMinutes(10);
        $user->save();

        // Enviar código por e-mail
        \Mail::raw("Seu código de verificação é: {$code}", function ($message) use ($user) {
            $message->to($user->email)->subject('Código de Verificação');
        });

        return response()->json([
            'message' => 'Código enviado para seu e-mail',
            '2fa_required' => true
        ]);
    }


    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout(Request $request)
    {
        $token = $request->input('refresh_token');
        $refreshToken = RefreshToken::where('token', $token)->first();

        if ($refreshToken) {
            $refreshToken->update(['revoked' => true]);
        }

        return response()->json(['message' => 'Logout efetuado no dispositivo']);
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
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->two_factor_code !== $request->code) {
            return response()->json(['error' => 'Código inválido'], 401);
        }

        if ($user->two_factor_expires_at->lt(now())) {
            return response()->json(['error' => 'Código expirado'], 401);
        }

        // Limpa código
        $user->two_factor_code = null;
        $user->two_factor_expires_at = null;
        $user->save();

        // Gera JWT de acesso
        $accessToken = auth()->login($user);

        // Cria refresh token para múltiplos dispositivos
        $refreshToken = RefreshToken::create([
            'user_id' => $user->id,
            'token' => base64_encode(Str::random(40)),
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'expires_at' => now()->addDays(30)
        ]);

        return response()->json([
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken->token,
            'token_type'    => 'bearer',
            'expires_in'    => auth()->factory()->getTTL() * 60
        ]);
    }

    public function refreshToken(Request $request)
    {
        $token = $request->input('refresh_token');
        $refreshToken = RefreshToken::where('token', $token)->first();

        if (!$refreshToken || !$refreshToken->isValid()) {
            return response()->json(['error' => 'Invalid refresh token'], 401);
        }

        $accessToken = auth()->login($refreshToken->user);

        return response()->json([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken->token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
