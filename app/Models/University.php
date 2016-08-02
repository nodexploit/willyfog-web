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

    public static function centres($university_id)
    {
        return json_decode(
            (new \App\Http\WebClient())
                ->request('GET', "/api/v1/universities/$university_id/centres")
                ->getBody()
        );
    }
}
