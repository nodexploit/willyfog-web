<?php

namespace App\Models;

class University
{
    public static function all()
    {
        return json_decode(
            (new \App\Http\WebClient())
                ->request('GET', '/api/v1/universities')
                ->getBody()
        );
    }
}
