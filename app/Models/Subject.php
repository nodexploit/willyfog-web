<?php

namespace App\Models;

class Subject
{
    public static function recognizerSubjects($user_id)
    {
        return json_decode(
            (new \App\Http\WebClient())
                ->request('GET', "/api/v1/users/$user_id/subjects")
                ->getBody()
        );
    }
}
