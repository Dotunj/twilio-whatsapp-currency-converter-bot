<?php

use App\CurrencyConverterClient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(CurrencyConverterClient $client)
    {
        $currencies = $client->getSupportedCurrencies();

        Log::info('Getting Currencies');

        foreach ($currencies as $currency => $key) {
            foreach ($key as $k) {
                DB::table('currencies')->insert([
                    'currency_name' => $k['currencyName'],
                    'currency_code' => $k['id']
                ]);
            }
        }
    }
}
