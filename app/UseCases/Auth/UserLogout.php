<?php


namespace App\UseCases\Auth;

use App\Models\User;

class UserLogout
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function call()
    {
        $this->user->token()->revoke();
    }
}
