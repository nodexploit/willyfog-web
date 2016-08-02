<?php


namespace App\Http\Controllers;


use App\Models\University;
use Slim\Http\Request;
use Slim\Http\Response;

class UniversityController
{
    public function centres(Request $request, Response $response, array $args)
    {
        $university_id = $args['id'];

        return $response->withJson(University::centres($university_id));
    }
}