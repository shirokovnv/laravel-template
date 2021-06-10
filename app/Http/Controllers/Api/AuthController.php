<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UserLoginRequest;
use App\Http\Requests\Auth\UserRegisterRequest;
use App\UseCases\Auth\CreateAccessAndRefreshToken;
use App\UseCases\Auth\ProvideMasterPassword;
use App\UseCases\Auth\RefreshToken;
use App\UseCases\Auth\UserLogin;
use App\UseCases\Auth\UserLogout;
use App\UseCases\Auth\UserRegister;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(UserLoginRequest $request)
    {

        $masterPassword = ProvideMasterPassword::getHash();

        $useLogin = new UserLogin(
            [
                'email' => $request->email,
                'password' => $request->password
            ],
            $masterPassword
        );

        $user = $useLogin->call();

        $useCreateToken = new CreateAccessAndRefreshToken($request->email, $request->password);
        $tokenInfo = $useCreateToken->call();

        return response()->json([
            'user' => $user,
            'token_info' => $tokenInfo
        ]);
    }

    public function register(UserRegisterRequest $request)
    {

        $useRegister = new UserRegister($request->validated());
        $useRegister->call();

        return response()->json(['message' => 'Successfully registered.']);
    }

    public function logout(Request $request)
    {
        $useLogout = new UserLogout($request->user('api'));
        $useLogout->call();

        return response()->json(['message' => 'Logout complete.']);
    }

    public function refresh(Request $request)
    {
        $useRefresh = new RefreshToken($request->token);
        $tokenInfo = $useRefresh->call();
        return response()->json(['token_info' => $tokenInfo]);
    }
}
