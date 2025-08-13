<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class XenditServices {
    protected $url;
    protected $client_id;
    protected $app_id;
    protected $secret_key;
    protected $xendit_url;

    public function __construct()
    {
        $this->url = $_ENV['XENDIT_URL'] ?? '';
        $this->client_id = $_ENV['XENDIT_CLIENT_ID'] ?? '';
        $this->app_id = $_ENV['XENDIT_APP_ID'] ?? '';
        $this->secret_key = $_ENV['XENDIT_SECRET_KEY'] ?? '';
        $this->xendit_url = $_ENV['XENDIT_URL'] ?? '';
    }
    public function createPaymentUrl($data)
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic '.$this->secret_key,
        ];
        $client = new Client();
        $request = new Request('POST', $this->xendit_url.'/api/v2/invoices', $headers, json_encode($data));
        $res = $client->sendAsync($request)->wait();

        return $res->getBody()->getContents();
    }
}