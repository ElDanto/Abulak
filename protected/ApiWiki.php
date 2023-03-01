<?php

namespace App;

class ApiWiki
{
    protected static $endPoint = "https://ru.wikipedia.org/w/api.php";


    public static function query(array $params = [])
    {
        $url = self::$endPoint . "?" . http_build_query( $params );

        $responseJson = file_get_contents($url);

        $response = json_decode( $responseJson, true );

        if (self::validationResponse($response)) {
            return $response['query'];
        }
        
    }

    public static function validationResponse(array $response)
    {
        if (is_array($response)) {
            if (isset($response['query'])) {
                $response = $response['query'];
                if(isset($response['search']) || isset($response['pages'])) {
                    if (!empty($response['search']) || !empty($response['pages'])) {
                        return true;
                    }
                }
            }
        }
        throw new \App\Exceptions\ApiQueryException("Ошибка импорта");
    }
}