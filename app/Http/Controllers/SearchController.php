<?php


namespace App\Http\Controllers;

use App\Http\Auth;
use App\Http\AuthorizedClient;
use App\Models\Degree;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class SearchController
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    public function results(Request $request, Response $response, $args)
    {
        $query = $request->getQueryParam('query');

        try {
            $res = (new AuthorizedClient)->request('GET', "/api/v1/equivalences", [
                'query' => [
                    'subjectName' => $query
                ]
            ]);

            $results = json_decode($res->getBody());
        } catch (\Exception $e) {
            $results = [];
        }

        return $this->ci->get('view')->render($response, 'search/results.twig', [
            'results' => $results,
        ]);
    }
}
