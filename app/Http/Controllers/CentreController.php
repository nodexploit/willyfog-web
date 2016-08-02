<?php


namespace App\Http\Controllers;


use App\Models\Centre;
use Slim\Http\Request;
use Slim\Http\Response;

class CentreController
{
    public function degrees(Request $request, Response $response, array $args)
    {
        $centre_id = $args['id'];

        return $response->withJson(Centre::degrees($centre_id));
    }
}