<?php

namespace App\Models;

class Centre
{
    public static function degrees($centre_id)
    {
        return json_decode(
            (new \App\Http\WebClient())
                ->request('GET', "/api/v1/centres/$centre_id/degrees")
                ->getBody()
        );
    }
}
