<?php

use App\CurrencyConverterClient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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

        foreach ($currencies as $currency => $key) {
            foreach ($key as $k) {
                DB::table('currencies')->insert([
                    'name' => $k['currencyName'],
                    'code' => $k['id']
                ]);
            }
        }
    }
}
