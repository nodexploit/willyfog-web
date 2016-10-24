<?php


namespace App\Http\Controllers;

use App\Http\Auth;
use App\Models\Subject;
use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class SubjectController
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }


    public function showRecognizerSubjects(Request $request, Response $response, array $args)
    {
        $auth = Auth::getInstance($this->ci);

        if (!$auth->isRecognizer()) {
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $recognizer_subjects = Subject::recognizerSubjects($auth->userId());

        return $this->ci->get('view')->render($response, 'recognizer/subjects.twig', [
            'subjects' => $recognizer_subjects
        ]);
    }

    public function deleteRecognizerSubject(Request $request, Response $response, array $args)
    {
        $recognizer_id = $args['id'];
        $subject_id = $args['sId'];

        $api_response = Subject::deleteRecognizerSubjects($recognizer_id, $subject_id);

        return $response->withJson([
            'success' => $api_response->status
        ]);
    }
}