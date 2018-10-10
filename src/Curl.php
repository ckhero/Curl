<?php
/**
 * Created by PhpStorm.
 * User: ckhero
 * Date: 2018/10/10
 * Time: 2:37 PM
 */

namespace Curl;


class Curl
{
    const METHOD_GET = 'GET';

    const METHOD_POST = 'POST';

    public $baseUrl;

    protected $curl;

    protected $options;

    protected $errorCode;

    protected $errorMsg;

    protected $httpCode;

    protected $defaultOptions = [
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36',
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false,
    ];


    public function get($url, array $proxy = [])
    {
        $this->setUrl($url);
        return $this->httpRequest(self::METHOD_GET, $proxy);
    }

    public function httpRequest($method, array $proxy)
    {
        $this->setOption(CURLOPT_CUSTOMREQUEST, $method);
        $this->setRandIp();
        if (!empty($proxy)) $this->setProxy($proxy);
        $curlOptions = $this->getOptions();
        $this->curl = curl_init($this->getUrl());
        curl_setopt_array($this->curl, $curlOptions);
        $response = curl_exec($this->curl);

        $this->httpCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

        if ($response === false) {
            $this->errorCode = curl_errno($this->curl);
            $this->errorMsg = curl_strerror($this->errorCode);
        }
        return $response;
    }

    public function setUrl($url)
    {
        $this->baseUrl = $url;
    }

    public function getUrl()
    {
        return $this->baseUrl;
    }

    public function setOption($key, $value)
    {
        if (array_key_exists($key, $this->defaultOptions)) {
            $this->defaultOptions[$key] = $value;
        } else {
            $this->options[$key] = $value;
        }
    }

    public function getRequestHeaders()
    {
        $requestHeaders = $this->getOption(CURLOPT_HTTPHEADER);
        $parseRequestHeaders = [];
        if (!$requestHeaders) return $parseRequestHeaders;

        foreach ($requestHeaders as $header) {
            list($key, $val) = explode(':', $header);
            $parseRequestHeaders[$key] = $val;
        }
        return $parseRequestHeaders;
    }

    public function getRequestHeader($key)
    {
        $parseRequestHeaders = $this->getRequestHeaders();
        return $parseRequestHeaders[$key] ?? null;
    }

    public function setRequestHeader($key, $val)
    {
        $parseRequestHeaders = array_merge($this->getRequestHeaders(), [$key => $val]);
        $requestHeaders = [];
        foreach ($parseRequestHeaders as $headerToSet => $valToSet) {
            $requestHeaders[] = $headerToSet . ':' . $valToSet;
        }

        $this->setOption(CURLOPT_HTTPHEADER, $requestHeaders);
        return $this;
    }

    public function setRequestHeaders($headers)
    {
        $parseRequestHeaders = array_merge($this->getRequestHeaders(), $headers);
        $requestHeaders = [];
        foreach ($parseRequestHeaders as $headerToSet => $valToSet) {
            $requestHeaders[] = $headerToSet . ':' . $valToSet;
        }

        $this->setOption(CURLOPT_HTTPHEADER, $requestHeaders);
        return $this;
    }

    public function getOption($key)
    {
        $curlOptions = $this->getOptions();
        return $curlOptions[$key] ?? false;
    }

    public function getHttpCode()
    {
        return $this->httpCode;
    }

    public function getOptions()
    {
        return $this->options + $this->defaultOptions;
    }

    public function getErrorInfo()
    {
        return [
            'code' => $this->errorCode,
            'msg' => $this->errorMsg
        ];
    }

    public function randIp()
    {
        $arr_1 = array("218","218","66","66","218","218","60","60","202","204","66","66","66","59","61","60","222","221","66","59","60","60","66","218","218","62","63","64","66","66","122","211");
        $randarr= mt_rand(0,count($arr_1));
        $ip1id = $arr_1[$randarr];
        $ip2id=  round(rand(600000,  2550000)  /  10000);
        $ip3id=  round(rand(600000,  2550000)  /  10000);
        $ip4id=  round(rand(600000,  2550000)  /  10000);
        return  $ip1id . "." . $ip2id . "." . $ip3id . "." . $ip4id;
    }

    public function setRandIp()
    {
        $this->setRequestHeaders([
            'X-FORWARDED-FOR' => $this->randIp(),
            'CLIENT-IP' => $this->randIp(),
            'REMOTE_ADDR' => $this->randIp(),
        ]);
    }

    public function setProxy($proxy)
    {
        $this->setOption(CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
        $this->setOption(CURLOPT_PROXY, $proxy['ip']);
        $this->setOption(CURLOPT_PROXYPORT, $proxy['port']);
    }
}