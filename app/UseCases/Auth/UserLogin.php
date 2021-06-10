<?php

namespace App\UseCases\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserLogin
{
    protected $credentials;
    protected $masterPasswordHash;

    public function __construct(array $credentials, string $masterPasswordHash)
    {
        $this->credentials = $credentials;
        $this->masterPasswordHash = $masterPasswordHash;
    }

    public function call()
    {
        $attempt = $this->loginWithMasterPassword();

        if ($attempt === false) {
            $attempt = Auth::attempt($this->credentials);
        }

        if ($attempt === false) {
            abort(422, "Invalid credentials.");
        }

        $user = Auth::user();

        if (!$user->hasVerifiedEmail()) {
            Auth::logout();
            abort("403", "You must verify your email first.");
        }

        return $user;
    }

    protected function loginWithMasterPassword(): bool
    {
        if ($this->isValidMasterPassword($this->credentials["password"])) {
            $user = User::where('email', $this->credentials['email'])->first();

            if ($user) {
                Auth::login($user);
                return true;
            }
        }

        return false;
    }

    protected function isValidMasterPassword(string $password): bool
    {
        return Hash::check($password, $this->masterPasswordHash);
    }
}
