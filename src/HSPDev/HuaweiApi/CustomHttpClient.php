<?php

namespace HSPDev\HuaweiApi;

/**
 * This library should really be using Guzzle or something,
 * but while hacking away I just had this laying around.
 * I've made some modifications to handle how the router API
 * rotates random tokens in the headers.
 */
class CustomHttpClient
{
    private $connectionTimeout = 3;
    private $responseTimeout = 5;

    //The API gives us cookie data in an API request, so manual cookies.
    private $manualCookieData = '';

    //We will have up to three tokens being used at once by the router? Whut?
    private $requestToken = '';
    private $requestTokenOne = '';
    private $requestTokenTwo = '';

    /**
     * We will call this, when we have parsed the data out from a login request.
     */
    public function setSecurity($cookie, $token)
    {
        $this->manualCookieData = $cookie;
        $this->requestToken = $token;
    }

    /**
     * We need the current token to make the login hash.
     */
    public function getToken()
    {
        return $this->requestToken;
    }

    /**
     * Builds the Curl Object.
     */
    private function getCurlObj($url, $headerFields = [])
    {
        $ch = curl_init();

        //curl_setopt($ch, CURLOPT_VERBOSE, true); // DEBUGGING

        $header = [
            'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8;charset=UTF-8',
            'Accept-Language: da-DK,da;q=0.8,en-US;q=0.6,en;q=0.4',
            'Accept-Charset: utf-8;q=0.7,*;q=0.7',
            'Keep-Alive: 115',
            'Connection: keep-alive',
            //The router expects these two to be there, but empty, when not in use.
            'Cookie: '.$this->manualCookieData,
            '__RequestVerificationToken: '.$this->requestToken,
        ];
        foreach ($headerFields as $h) {
            $header[] = $h;
        }

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectionTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->responseTimeout);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip'); //The router is fine with this, so no problem.
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        //The router rotates tokens in the response headers randomly, so we will parse them all.
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, [$this, 'HandleHeaderLine']);

        return $ch;
    }

    //end function

    /**
     * Makes HTTP POST requests containing XML data to the router.
     */
    public function postXml($url, $xmlString)
    {
        //The API wants it like this.
        $ch = $this->getCurlObj($url, ['Content-Type: text/plain; charset=UTF-8', 'Cookie2: $Version=1']);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlString);

        $result = curl_exec($ch);
        curl_close($ch);
        if (!$result) {
            throw new \Exception('A network error occured with cURL.');
        }

        return $result;
    }

    /**
     * Handles the HTTP Response header lines from cURL requests,
     * so we can extract all those tokens.
     */
    public function HandleHeaderLine($curl, $header_line)
    {

        /*
        * Not the prettiest way to parse it out, but hey it works.
        * If adding more or changing, remember the trim() call
        * as the strings have nasty null bytes.
        */
        if (strpos($header_line, '__RequestVerificationTokenOne') === 0) {
            $token = trim(substr($header_line, strlen('__RequestVerificationTokenOne:')));
            $this->requestTokenOne = $token;
        } elseif (strpos($header_line, '__RequestVerificationTokenTwo') === 0) {
            $token = trim(substr($header_line, strlen('__RequestVerificationTokenTwo:')));
            $this->requestTokenTwo = $token;
        } elseif (strpos($header_line, '__RequestVerificationToken') === 0) {
            $token = trim(substr($header_line, strlen('__RequestVerificationToken:')));
            $this->requestToken = $token;
        } elseif (strpos($header_line, 'Set-Cookie:') === 0) {
            $cookie = trim(substr($header_line, strlen('Set-Cookie:')));
            $this->manualCookieData = $cookie;
        }

        return strlen($header_line);
    }

    /**
     * Performs a HTTP GET to the specified URL.
     */
    public function get($url)
    {
        $ch = $this->getCurlObj($url);

        $result = curl_exec($ch);
        curl_close($ch);
        if (!$result) {
            throw new \Exception('A network error occured with cURL.');
        }

        return $result;
    }
}
