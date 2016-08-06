<?php

namespace App\Models;

class Country
{
    public static function all()
    {
        return json_decode(
            (new \App\Http\WebClient())
                ->request('GET', "/api/v1/countries")
                ->getBody()
        );
    }

    public static function cities($country_id)
    {
        return json_decode(
            (new \App\Http\WebClient())
                ->request('GET', "/api/v1/countries/$country_id/cities")
                ->getBody()
        );
    }
}
