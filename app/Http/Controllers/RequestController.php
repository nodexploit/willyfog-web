<?php


namespace App\Http\Controllers;

use App\Http\Auth;
use App\Http\AuthorizedClient;
use App\Models\Country;
use App\Models\Degree;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class RequestController
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    public function index(Request $request, Response $response, $args)
    {
        $user_id = Auth::getInstance($this->ci)->userId();

        try {
            $res = (new AuthorizedClient)->request('GET', "/api/v1/users/$user_id/requests");

            $requests = json_decode($res->getBody());
        } catch (\Exception $e) {
            $requests = [];
        }

        return $this->ci->get('view')->render($response, 'requests/index.twig', [
            'requests' => $requests,
        ]);
    }

    public function show(Request $request, Response $response, $args)
    {
        $request_id = $args['id'];

        try {
            $res = (new AuthorizedClient)->request('GET', "/api/v1/requests/$request_id");

            $eq_request = json_decode($res->getBody());

            $res = (new AuthorizedClient)->request('GET', "/api/v1/requests/$request_id/comments");

            $comments = json_decode($res->getBody());
        } catch (\Exception $e) {
            $eq_request = [];
            $comments = [];
        }

        return $this->ci->get('view')->render($response, 'requests/show.twig', [
            'request'   => $eq_request,
            'comments'  => $comments
        ]);
    }

    public function form(Request $request, Response $response, $args)
    {
        $user = Auth::getInstance($this->ci)->user();
        $degree_name = $user->degree_name;
        $degree_id = $user->degree_id;

        $subjects = Degree::subjects($degree_id);
        $countries = Country::all();
        
        return $this->ci->get('view')->render($response, 'requests/create.twig', [
            'degree_name'       => $degree_name,
            'subjects'          => $subjects,
            'subject_codes'     => json_encode(array_column($subjects, 'code')),
            'subject_credits'   => json_encode(array_column($subjects, 'credits')),
            'countries'         => $countries
        ]);
    }

    public function create(Request $request, Response $response, $args)
    {
        $request_content = $request->getParsedBodyParam('request_content');

        $res = (new AuthorizedClient())->request('POST', '/api/v1/requests', [
            'form_params' => [
                'request_content' => $request_content
            ]
        ]);

        $api_response = json_decode($res->getBody());

        if ($api_response->status == 'Success') {
            $request_id = $api_response->data;

            return $response->withStatus(302)->withHeader('Location', "/requests/$request_id");
        } else {
            $this->ci->get('flash')->addMessage('error', $api_response->status);
            $this->ci->get('flash')->addMessage('messages', implode(', ', $api_response->messages));

            return $response->withStatus(302)->withHeader('Location', "/requests/new");
        }
    }

    public function comment(Request $request, Response $response, $args)
    {
        $request_id = $args['id'];
        $comment = $request->getParsedBodyParam('comment');

        $res = (new AuthorizedClient())->request('POST', "/api/v1/requests/$request_id/comment", [
            'form_params' => [
                'content'   => $comment
            ]
        ]);

        $api_response = json_decode($res->getBody());

        if ($api_response->status == 'Success') {
            return $response->withStatus(302)->withHeader('Location', "/requests/$request_id");
        } else {
            $this->ci->get('flash')->addMessage('error', $api_response->status);
            $this->ci->get('flash')->addMessage('messages', implode(', ', $api_response->messages));

            return $response->withStatus(302)->withHeader('Location', "/requests/$request_id");
        }
    }
}
