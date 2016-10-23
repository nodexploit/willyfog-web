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
}
