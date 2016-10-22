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

        $recognizerSubjects = Subject::recognizerSubjects($auth->userId());

        return $this->ci->get('view')->render($response, 'recognizer/subjects.twig', [
            'subjects' => $recognizerSubjects
        ]);
    }
}