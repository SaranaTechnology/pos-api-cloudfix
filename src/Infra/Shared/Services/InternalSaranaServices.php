<?php

namespace Infra\Shared\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class InternalSaranaServices
{
    protected $url;

    protected $client_id;

    protected $app_id;

    public function __construct()
    {
        $this->url = $_ENV['INTERNAL_SARANA_URL'] ?? '';
        $this->client_id = $_ENV['INTERNAL_SARANA_CLIENT_ID'] ?? '';
        $this->app_id = $_ENV['INTERNAL_SARANA_APP_ID'] ?? '';
    }

    public function getClientData()
    {
        $client = new Client();
        $request = new Request('GET', $this->url.'/api/v1/client/'.$this->client_id);
        $res = $client->sendAsync($request)->wait();

        return $res->getBody()->getContents();
    }
    public function createWithdrawalAction($data, $id)
    {
        $headers = [
            'Content-Type' => 'application/json',
        ];
        $data['client_id'] = $this->client_id;
        $data['url'] = env('APP_URL').'/api/v1/withdrawal/'.$id;
        $client = new Client();
        $request = new Request('POST', $this->url.'/api/v1/withdrawal', $headers, json_encode($data));
        try {
            $res = $client->sendAsync($request)->wait();

            return $res->getBody()->getContents();
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                $responseBody = $response->getBody()->getContents();

                return $responseBody;
            }

            return $this->CreateWithdrawalAction($data, $id);
        }
    }
    public function generatedPlan($transaction){
        $data['client_id'] = $transaction->client_id;
        $data['plan_id'] = $transaction->plan_id;

        $client = new Client();
        $request = new Request('POST', $this->url.'/api/v1/subsription', ['Content-Type' => 'application/json'],json_encode($data));
        $res = $client->sendAsync($request)->wait();
        return $res->getBody()->getContents();

    }
    public function deleteWithdrawal($id)
    {
        $client = new Client();
        $request = new Request('Delete', $this->url.'/api/v1/withdrawal/'.$id);
        try {
            $res = $client->sendAsync($request)->wait();

            return $res->getBody()->getContents();
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                $responseBody = $response->getBody()->getContents();

                return $responseBody;
            }

            return $this->deleteWithdrawal($id);
        }
    }
    public function updateClientData($data)
    {
        $headers = [
            'Content-Type' => 'application/json',
        ];
        $client = new Client();
        $request = new Request('POST', $this->url.'/api/v1/client/'.$this->client_id, $headers, json_encode($data));
        $res = $client->sendAsync($request)->wait();

        return $res->getBody()->getContents();
    }
    public function getListPlan($query){
        $client = new Client();
        // Tambahkan parameter default
        $query['client_id'] = $this->client_id;
        // Bangun query string dari array
        $queryString = http_build_query($query);
        // Buat request dengan query string
        $request = new Request('GET', $this->url.'/api/v1/plan?'.$queryString);
        $res = $client->sendAsync($request)->wait();

        return $res->getBody()->getContents();
    }
    public function sendLogToTransaction($data)
    {
        $data['client_id'] = $this->client_id;
        $data['transaction_log'] = json_encode($data['transaction_log']);
        $headers = [
            'Content-Type' => 'application/json',
        ];
        $client = new Client();
        $request = new Request('POST', $this->url.'/api/v1/transaction', $headers, json_encode($data));
        $res = $client->sendAsync($request)->wait();
        return $res->getBody()->getContents();
    }
    public function getSubsription($requestHttp)  {
        $client = new Client();
        // Ambil query parameter dari requestHttp jika ada
        $queryParams = http_build_query(array_merge(['client_id' => $this->client_id], $requestHttp));
        // Buat request dengan query params yang dinamis
         $url = $this->url . '/api/v1/subsription?' . $queryParams;
        $request = new Request('GET', $url);
        $res = $client->sendAsync($request)->wait();
        return $res->getBody()->getContents();    
    }
    public function getPlanDataDetail($id){
        $client = new Client();
        $request = new Request('GET', $this->url.'/api/v1/plan/'.$id);
        $res = $client->sendAsync($request)->wait();
        return $res->getBody()->getContents();
    }
    public function getTransactionFee()
    {
        $client = new Client();
        $request = new Request('GET', $this->url.'/api/v1/apps/'.$this->app_id.'?with=transaction_fee');
        $res = $client->sendAsync($request)->wait();
        return $res->getBody()->getContents();
    }
}
