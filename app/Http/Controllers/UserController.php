<?php


namespace App\Http\Controllers;

use App\Http\AuthorizedClient;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController
{
    public function notifications(Request $request, Response $response, array $args)
    {
        $user_id = $args['id'];

        $res = (new AuthorizedClient())->request('GET', "/api/v1/users/$user_id/notifications");

        $api_response = json_decode($res->getBody());

        return $response->withJson($api_response);
    }
}