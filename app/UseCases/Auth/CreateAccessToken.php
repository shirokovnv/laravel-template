<?php


namespace App\UseCases\Auth;

use App\Models\User;

class CreateAccessToken
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function call()
    {

        $tokenResult = $this->user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->save();

        return $tokenResult;
    }
}
