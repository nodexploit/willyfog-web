<?php


namespace App\Http\Controllers;

use App\Models\Degree;
use Slim\Http\Request;
use Slim\Http\Response;

class DegreeController
{
    public function subjects(Request $request, Response $response, array $args)
    {
        $degree_id = $args['id'];

        return $response->withJson(Degree::subjects($degree_id));
    }
}