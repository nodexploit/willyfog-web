<?php

namespace App\Models;

class Degree
{
    public static function subjects($degree_id)
    {
        return json_decode(
            (new \App\Http\WebClient())
                ->request('GET', "/api/v1/degrees/$degree_id/subjects")
                ->getBody()
        );
    }
}
