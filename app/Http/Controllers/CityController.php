<?php


namespace App\Http\Controllers;

use App\Models\City;
use Slim\Http\Request;
use Slim\Http\Response;

class CityController
{
    public function universities(Request $request, Response $response, array $args)
    {
        $city_id = $args['id'];

        return $response->withJson(City::universities($city_id));
    }
}