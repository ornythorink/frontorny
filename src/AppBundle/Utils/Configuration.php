<?php

namespace AppBundle\Utils;


class Configuration
{
    public static function getApiUrl($env)
    {
        $url = null;
        if($env === 'dev')
        {
            $url = 'http://127.0.0.1:8001/';
        } else {
            $url = "http://apishoes.ovh/";
        }
        return $url;
    }
}
