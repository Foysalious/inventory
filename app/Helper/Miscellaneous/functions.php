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
            if (count($trace) == 1) {
                $trace[1] = "";
            }
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

if (!function_exists('getIp')) {
    /**
     * @return string
     */
    function getIp()
    {
        $ip_methods = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        foreach ($ip_methods as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); //just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return request()->ip();
    }
}

if (!function_exists('constants')) {
    /**
     * Get the constant from config constants file.
     *
     * @param String $key
     * @return mixed
     */
    function constants($key)
    {
        return config('constants.' . $key);
    }
}
