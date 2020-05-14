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
        if ($response->status() != 200) {
            Log::critical("The response HTTP code of get data API is: " . $response->status());
        }

        Log::log($response->body());
//        $xml = simplexml_load_string($response->body());
//        $json = json_encode($xml);
//        $array = json_decode($json,TRUE);

        $response = Http::get("https://api.telegram.org/bot1138407370:AAGcehBntpDFAD8fOsRiOf-iLOV3oV0ovJI/sendMessage?chat_id=@IranianEarthquakes&text=Salam!");
        Log::info("api response code: " . $response->status());
    }
}
