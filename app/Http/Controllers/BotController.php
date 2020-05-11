<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CurrencyConverterClient;
use Twilio\TwiML\MessagingResponse;
use App\Currency;
use Illuminate\Support\Facades\Log;

class BotController extends Controller
{
    protected $client;

    public function __construct(CurrencyConverterClient $client)
    {
        $this->client = $client;
    }

    public function sendReplies(Request $request)
    {
        $response = new MessagingResponse();

        // $message = $response->message('');

        $body = $request->input('Body');

        Log::info("Received Payload: {$body}");

        $content = $this->determineMessageContent($body);

        Log::info("Received Message: {$content}");

        $response->message($content);

        return $response;
    }

    private function determineMessageContent(string $content)
    {
        $formatContent = strtolower($content);

        if (strpos($formatContent, 'hello') !== false) {
            $message = "Welcome to the WhatsApp Bot for Currency Conversion \n";
            $message .= "Use the following format to chat with the bot \n";
            $message .= "Convert 5 USD to NGN \n";
            return $message;
        }

        if (strpos($formatContent, 'convert') !== false) {
            return $this->formatContentForConversion($formatContent);
        }

        return $this->formatContentForInvalidMessageFormat();
    }

    private function formatContentForConversion($formatContent)
    {
        $contentInArray = explode(" ", $formatContent);
        $itemsInArray = count($contentInArray);

        if ($itemsInArray < 5 || $itemsInArray > 5) {
            return $this->formatContentForInvalidMessageFormat();
        }

        return $this->performConversion($contentInArray);
    }

    private function formatContentForInvalidMessageFormat()
    {
        $message = "The Conversion Format is Invalid \n";
        $message .= "Please use the format \n";
        $message .= "Convert 5 USD to NGN";

        return $message;
    }

    private function performConversion(array $contentInArray)
    {
        $amount = $contentInArray[1];
        $baseCurrency = strtoupper($contentInArray[2]);
        $toCurrency = strtoupper($contentInArray[4]);

        $items = $this->getCurrencyCode($baseCurrency, $toCurrency);

        if ($items->count() < 2) {
            return "Please enter a valid Currency Code";
        }

        $convertedAmount = $this->client->convertCurrency($amount, $baseCurrency, $toCurrency);

        return "{$amount} {$baseCurrency} is {$convertedAmount} {$toCurrency}";
    }

    private function getCurrencyCode(string $baseCurrency, string $currency)
    {
        $items = [$baseCurrency, $currency];

        $currencyCode = Currency::findByCurrencyCode($items);

        return $currencyCode;
    }
}
