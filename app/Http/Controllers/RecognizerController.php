<?php


namespace App\Http\Controllers;

use App\Http\Auth;
use App\Http\AuthorizedClient;
use App\Models\Recognizer;
use App\Models\Role;
use App\Models\Subject;
use Slim\Http\Request;
use Slim\Http\Response;
use Interop\Container\ContainerInterface;
use App\Http\WebClient;

class RecognizerController
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    public function showRecognizers(Request $request, Response $response, array $args)
    {
        $auth = Auth::getInstance($this->ci);

        if (!$auth->isAdmin() && !$auth->isCoordinator()) {
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $centre_id = $auth->user()->centre_id;

        return $this->ci->get('view')->render($response, 'coordinator/recognizers.twig', [
            'recognizers' => \App\Models\Centre::recognizers($centre_id)
        ]);
    }

    public function show(Request $request, Response $response, array $args)
    {
        $auth = Auth::getInstance($this->ci);

        if (!$auth->isAdmin() && !$auth->isCoordinator()) {
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $recognizer_id = $args['id'];

        $subjects_index = Subject::index();

        return $this->ci->get('view')->render($response, 'recognizer/show.twig', [
            'recognizer_id' => $recognizer_id,
            'recognizer' => \App\Models\Recognizer::find($recognizer_id),
            'subjects' => Subject::recognizerSubjects($recognizer_id),
            'subjects_index' => $subjects_index
        ]);
    }

    public function addSubjects(Request $request, Response $response, array $args)
    {
        $auth = Auth::getInstance($this->ci);

        if (!$auth->isAdmin() && !$auth->isCoordinator()) {
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $params = $request->getParsedBody();
        $recognizer_id = $args['id'];
        
        $api_response = Recognizer::addSubjects($recognizer_id, json_encode($params['subject_ids']));
        
        return $response->withJson([
            'success' => $api_response->status
        ]);
    }
}