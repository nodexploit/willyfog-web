<?php


namespace App\Http\Controllers;

use App\Models\Country;
use Slim\Http\Request;
use Slim\Http\Response;

class CountryController
{
    public function cities(Request $request, Response $response, array $args)
    {
        $country_id = $args['id'];

        return $response->withJson(Country::cities($country_id));
    }
}