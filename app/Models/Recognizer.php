<?php

namespace App\Models;

class Recognizer
{
    public static function find($recognizer_id)
    {
        return json_decode(
            (new \App\Http\WebClient())
                ->request('GET', "/api/v1/users/$recognizer_id/info")
                ->getBody()
        );
    }

    public static function addSubjects($recognizer_id, $subject_ids)
    {
        return json_decode(
            (new \App\Http\AuthorizedClient())
                ->request('POST', "/api/v1/users/$recognizer_id/subjects", [
                    'form_params' => [
                        'subject_ids' => $subject_ids
                    ]
                ])
                ->getBody()
        );
    }
}
