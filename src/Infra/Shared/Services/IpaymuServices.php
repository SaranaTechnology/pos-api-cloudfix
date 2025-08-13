<?php

namespace Infra\Shared\Services;

use Illuminate\Support\Facades\Auth;
use iPaymu\iPaymu;

class IpaymuServices
{
    protected $url;

    protected $api_key;

    protected $va;

    protected $client_id;

    protected $returnUrl;
    protected $production;

    protected $app_id;

    public function __construct()
    {
        $this->url = env('IPAYMU_URL');
        $this->api_key = env('IPAYMU_API_KEY');
        $this->va = env('IPAYMU_VA');
        $this->client_id = env('INTERNAL_SARANA_CLIENT_ID');
        $this->app_id = env('INTERNAL_SARANA_APP_ID');
        $this->production = false;
        if (env('IPAYMU_ENV') == 'PRODUCTION') {
            $this->production = true;
        }

    }

    public function getURL(array $body, string $product)
    {
        $ipaymu = new iPaymu(apiKey: $this->api_key, va: $this->va, production: $this->production);


        $ipaymu->setURL([
            'ureturn' => 'https://sgp.pendidikancerdas.com/wali/transaction',
            'unotify' => route('callback'),
            'ucancel' => 'https://sgp.pendidikancerdas.com/wali/transaction',
        ]);
        $productName = str_replace("\u00a0", ' ', $body['name']);

        $desc = $body['plan_id'].','.$this->client_id.','.$this->app_id;
        $productsName = [$productName];
        $productsPrice = [$body['price']];
        $productsQty = [1];
        $productsDesc = [$desc];
        $productsLength = [];
        $productsWidth = [];
        $productsHeight = [];
        /*$ipaymu->setBuyer([
            'name'=>Auth::user()->name,
            'email'=>Auth::user()->email
        ]);*/
        $params['product'] = $productsName ?? null;
        $params['price'] = $productsPrice ?? null;
        $params['quantity'] = $productsQty ?? null;
        $params['description'] = $productsDesc ?? null;
        $params['length'] = $productsLength ?? null;
        $params['width'] = $productsWidth ?? null;
        $params['height'] = $productsHeight ?? null;
        $ipaymu->addCart($params);
        return $ipaymu->redirectPayment();
    }
}
