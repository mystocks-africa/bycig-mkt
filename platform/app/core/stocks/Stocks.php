<?php
namespace App\Core\Stocks;

class Stocks {
    public static function getSupportedStocks(): array
    {
        $supportedStocks = [
            "AAPL" => "Apple",
            "TSLA" =>  "Tesla",
            "AMZN" => "Amazon",
            "GOOGL" => "Alphabet (Google)",
            "MSFT" => "Microsoft",
            "NVDA" => "NVIDIA",
            "META" =>  "Meta Platforms (Facebook)",
            "NFLX" =>  "Netflix",
            "BRK.B" => "Berkshire Hathaway",
            "JPM" =>   "JPMorgan Chase",
            "V" =>  "Visa",
            "DIS" => "Disney",
            "PYPL" => "PayPal",
            "INTC" =>  "Intel",
            "KO"  => "Coca-Cola"
        ];

        return $supportedStocks;
    }
}