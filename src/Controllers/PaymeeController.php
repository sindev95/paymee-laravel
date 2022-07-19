<?php

namespace Sindev95\Paymee\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use function GuzzleHttp\Promise\all;

class PaymeeController extends  Controller
{
    protected Client $client;
    protected string $gateway_url;
    protected string $service_url;
    protected string $check_url;

    public function __construct(Client $client)
    {
        if (config('paymee.mode') == 'test') {
            $this->gateway_url = 'https://sandbox.paymee.tn/gateway/';
            $this->service_url = 'https://sandbox.paymee.tn/api/v1/payments/create';
            $this->check_url = 'https://sandbox.paymee.tn/api/v1/payments/payment_token/check';
        } elseif (config('paymee.mode') == 'live') {
            $this->gateway_url = 'https://app.paymee.tn/gateway/';
            $this->service_url = 'https://app.paymee.tn/api/v1/payments/create';
            $this->check_url = 'https://app.paymee.tn/api/v1/payments/payment_token/check';
        }

        $this->client = $client;
    }

    public function generate_paymee_form($order_id, $total)
    {
        $headers = array('Authorization' => "Token " . config('paymee.private_key'));
        $amount_dinars = floatval($total) * floatval(config('paymee.exchange_rate'));
        $body = array(
            'vendor' => config('paymee.merchant_id'),
            'amount' => $amount_dinars,
            'note' => "Commande #" . $order_id
        );
        $args = [
            'form_params' => $body,
            'timeout' => '5',
            'redirection' => '5',
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => $headers,
            'cookies' => [],
        ];
        try {
            $response = $this->client->post($this->service_url, $args);
            $data = json_decode($response->getBody()->getContents());

            $view_data = array(
                'url' => $this->gateway_url,
                'token' => $data->data->token,
                'url_ok' => route('paymee.success'),
                'url_ko' => route('paymee.failed'),
            );
            return view('paymee::index', $view_data);
        } catch (GuzzleException $e) {
            dd($e->getMessage());
        }
    }

    public function paymee_success(){
        $this->check_url = str_replace('payment_token',request()->get('payment_token'),$this->check_url);

        $headers = array('Authorization' => "Token " . env('paymee_private_key'));
        $args = array('headers'     => $headers,);

        $response = $this->client->get($this->check_url, $args);
        $response = json_decode($response->getBody()->getContents(),true);
        if ($response['data']['payment_status']) {
            return view('paymee::success');
        }
        return view('paymee::failed');
    }

    public function paymee_failed(){
        return view('paymee::failed');
    }
}
