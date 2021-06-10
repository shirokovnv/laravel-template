<?php


namespace App\UseCases\Auth;

use Laravel\Passport\Client;
use Illuminate\Http\Response;
use League\OAuth2\Server\AuthorizationServer;
use Psr\Http\Message\ServerRequestInterface;
use Nyholm\Psr7\Response as Psr7Response;

class RefreshToken
{
    protected $refreshToken;
    protected $scope;

    public function __construct(string $refreshToken, string $scope = "") {
        $this->refreshToken = $refreshToken;
        $this->scope = $scope;
    }

    public function call() {
        $oClient = Client::where('password_client', true)->firstOrFail();

        $server = app()->make(AuthorizationServer::class);
        $request = app()->make(ServerRequestInterface::class);
        $request = $request->withParsedBody(
            [
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->refreshToken,
                'client_id' => $oClient->id,
                'client_secret' => $oClient->secret,
                'scope' => $this->scope,
            ]
        );

        $psrResponse = $server->respondToAccessTokenRequest($request, new Psr7Response);

        $response = new Response(
            $psrResponse->getBody(),
            $psrResponse->getStatusCode(),
            $psrResponse->getHeaders()
        );

        return json_decode((string) $response->content(), true);
    }

}
