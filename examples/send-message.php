<?php

declare(strict_types = 1);

include __DIR__.'/basics.php';

use React\EventLoop\Factory;
use unreal4u\TelegramAPI\HttpClientRequestHandler;
use unreal4u\TelegramAPI\Telegram\Methods\SendMessage;
use unreal4u\TelegramAPI\TgLog;

require 'BinanceClass.php';

$response = file_get_contents("https://www.paribu.com/ticker");
$json = json_decode($response, true);

$last_value = $json['BTC_TL']['last'];
// $lowest_bid = $json['BTC_TL']['lowestAsk'];
// $highest_bid = $json['BTC_TL']['highestBid'];
// $percentage_change = $json['BTC_TL']['percentChange'];
// $volume = $json['BTC_TL']['volume'];
// $highest24hr = $json['BTC_TL']['high24hr'];
// $lowest24hr = $json['BTC_TL']['low24hr'];

//$bittrext_response = file_get_contents("https://bittrex.com/api/v1.1/public/getticker?market=USDT-BTC");
//$bittrext_json_response = json_decode($bittrext_response, true);
//$bittrex_last_value = $bittrext_json_response['result']['Last'];

$api = new Binance(BINANCE_PUBLIC_KEY, BINANCE_SECRET_KEY);

$ticker = $api->prices();

$loop = Factory::create();
$tgLog = new TgLog(BOT_TOKEN, new HttpClientRequestHandler($loop));

// echo "<pre>"; 
 // print_r($ticker); 
// echo "/<pre>"; 

$sendMessage = new SendMessage();
$sendMessage->chat_id = A_USER_CHAT_ID;
$sendMessage->text =
				           'BTCTRY : '. $last_value . ' TL '
				  . "\n" . 'BTCUSD : '. $ticker['BTCUSDT'] . ' USD'
				  . "\n" . 'BCNBTC : '. $ticker['BCNBTC'] . ' Bought at 0.00000246';
				 
$promise = $tgLog->performApiRequest($sendMessage);

$promise->then(
    function ($response) {
        echo '<pre>';
        var_dump($response);
        echo '</pre>';
    },
    function (\Exception $exception) {
        // Onoes, an exception occurred...
        echo 'Exception ' . get_class($exception) . ' caught, message: ' . $exception->getMessage();
    }
);

$loop->run();
