<?php

namespace PayBy\Util;

use PayBy\Error;

class RequestOptions
{
    public $headers;
    public $privateKey;
    public $signOpts;

    public function __construct($key = null, $headers = [], $signOpts = [])
    {
        $this->privateKey = $key;
        $this->headers = $headers;
        $this->signOpts = $signOpts;
    }

    /**
     * Unpacks an options array and merges it into the existing RequestOptions
     * object.
     * @param array|string|null $options a key => value array
     *
     * @return RequestOptions
     */
    public function merge($options)
    {
        $other_options = self::parse($options);
        if ($other_options->privateKey === null) {
            $other_options->privateKey = $this->privateKey;
        }
        $other_options->headers = array_merge($this->headers, $other_options->headers);
        return $other_options;
    }

    /**
     * Unpacks an options array into an RequestOptions object
     * @param array|string|null $options a key => value array
     *
     * @return RequestOptions
     */
    public static function parse($options)
    {
        if ($options instanceof self) {
            return $options;
        }

        if (is_null($options)) {
            return new RequestOptions(null, []);
        }

        if (is_string($options)) {
            return new RequestOptions($options, []);
        }

        if (is_array($options)) {
            $headers = [];
            $key = null;
            $signOpts = [];
            if (array_key_exists('api_key', $options)) {
                $key = $options['api_key'];
            }
            if (array_key_exists('sign_opts', $options)) {
                $signOpts = $options['sign_opts'];
            }
            return new RequestOptions($key, $headers, $signOpts);
        }

        $message = 'The second argument to PayBy API method calls is an '
           . 'optional per-request privateKey, which must be a string, or '
           . 'per-request options, which must be an array. (HINT: you can set '
           . 'a global privateKey by "PayBy::setPrivateKey(<privateKey>)")';
        throw new Error\Api($message);
    }

    /**
     * @param array $opts The custom options
     * @param array $signOpts The sign options
     * @return array The merged options
     */
    public static function parseWithSignOpts($opts, $signOpts)
    {
        $options = self::parse($opts);
        $options->signOpts = array_merge($options->signOpts, $signOpts);
        return $options;
    }

    /**
     * @param array $signOpts The sign options
     * @return array The merged options
     */
    public function mergeSignOpts($signOpts)
    {
        $this->signOpts = array_merge($this->signOpts, $signOpts);
        return $this;
    }
}
