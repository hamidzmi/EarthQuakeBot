<?php


namespace App\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendMessageCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'send-message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "send message to telegram channel";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $response = Http::get("http://irsc.ut.ac.ir/events_list.xml");
        $xml = simplexml_load_string($response->body());
        $json = json_encode($xml);
        $events = json_decode($json,TRUE);
//        dd($events['item'][1]);
        $lastEvent = $events['item'][1];
        $message = "Region: " . $lastEvent['reg1'] . "\n" .
            "Depth: " . $lastEvent['dep'] . "\n" .
            "Time: " . $lastEvent['date'] . "\n" .
            "Location: https://www.google.com/maps/search/?api=1&query=".$lastEvent['lat'].",".$lastEvent['long']."\n";
        Http::get("https://api.telegram.org/bot1138407370:AAGcehBntpDFAD8fOsRiOf-iLOV3oV0ovJI/sendMessage?chat_id=@IranianEarthquakes&text=" . $message);
//        foreach ($events as $i => $event) {
//            if ($i == 0) {
//                continue;
//            }
//            if (i > 1) {
//                break;
//		    }
//            $message = sprintf(
//                "Region:%s\nDepth:%s\nTime:%s\nLocation:https://www.google.com/maps/search/?api=1&query=%s,%s\n", [
//                    $event["reg1"],
//                    $event["dep"],
//                    $event["date"],
//                    $event["lat"],
//                    $event["long"],
//                ]);
//            Http::get("https://api.telegram.org/bot1138407370:AAGcehBntpDFAD8fOsRiOf-iLOV3oV0ovJI/sendMessage?chat_id=@IranianEarthquakes&text=" . $message);
//        }
    }
}
