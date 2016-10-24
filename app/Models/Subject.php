<?php

namespace App\Models;

class Subject
{
    public static function index()
    {
        return json_decode(
            (new \App\Http\WebClient())
                ->request('GET', '/api/v1/subjects')
                ->getBody()
        );
    }

    public static function recognizerSubjects($user_id)
    {
        return json_decode(
            (new \App\Http\WebClient())
                ->request('GET', "/api/v1/users/$user_id/subjects")
                ->getBody()
        );
    }

    public static function deleteRecognizerSubjects($recognizer_id, $subject_id)
    {
        return json_decode(
            (new \App\Http\AuthorizedClient())
                ->request('DELETE', "/api/v1/users/$recognizer_id/subjects/$subject_id")
                ->getBody()
        );
    }
}
