<?php

namespace App\Models;

class City
{
    public static function universities($city_id)
    {
        return json_decode(
            (new \App\Http\WebClient())
                ->request('GET', "/api/v1/cities/$city_id/universities")
                ->getBody()
        );
    }
}
