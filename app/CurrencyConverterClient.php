<?php

namespace App;

use Illuminate\Support\Facades\Http;


class CurrencyConverterClient
{
    protected $baseUrl = 'https://free.currconv.com/api/v7';

    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.currency_converter.api_key');
    }

    public function getSupportedCurrencies()
    {
        $url = "{$this->baseUrl}/currencies?apiKey={$this->apiKey}";
        $response = Http::get($url);
        if ($response->ok()) {
            return $response->json();
        }
        return $response->throw();
    }

    public function convertCurrency($amount, $baseCurrency, $toCurrency)
    {
        $query = "{$baseCurrency}_{$toCurrency}";
        $url = "{$this->baseUrl}/convert?q={$query}&compact=ultra&apiKey={$this->apiKey}";
        $response = Http::get($url);
        if ($response->ok()) {
            $conversion = $response->json();
            $baseConversion = floatval($conversion[$query]);
            $total = $amount * $baseConversion;
            return number_format($total, 2);
        }
        return $response->throw();
    }
}
