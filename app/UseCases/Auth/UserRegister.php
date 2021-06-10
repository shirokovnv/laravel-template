<?php


namespace App\UseCases\Auth;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UserRegister
{
    public $userData;

    public function __construct(array $userData)
    {
        $this->userData = $userData;
    }

    public function call()
    {
        $user = new User;
        $user->fill($this->userData);
        $user->password = Hash::make($user->password);
        $user->email_verified_at = Carbon::now()->format('Y-m-d H:i:s');
        return $user->save();
    }
}
