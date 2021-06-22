<?php

if (!function_exists('simplifyExceptionTrace')) {
    /**
     * @param \Exception $e
     * @return array
     */
    function simplifyExceptionTrace(\Exception $e)
    {
        return collect(explode(PHP_EOL, $e->getTraceAsString()))->mapWithKeys(function ($trace) {
            $trace = explode(": ", preg_replace('/^(#\d+ )(.*)$/', '$2', $trace));
            if (count($trace) == 1) $trace[1] = "";
            return [$trace[0] => $trace[1]];
        })->all();
    }
}

if (!function_exists('decodeGuzzleResponse')) {
    /**
     * @param      $response
     * @param      bool $assoc
     * @return     object|array|string|null
     */
    function decodeGuzzleResponse($response, $assoc = true)
    {
        $string = $response->getBody()->getContents();
        $result = json_decode($string, $assoc);
        if (json_last_error() != JSON_ERROR_NONE && $string != "") {
            $result = $string;
        }
        return $result;
    }
}
