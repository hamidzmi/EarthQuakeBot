<?php


namespace App\Console\Commands;


use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

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

        $lastUpdate = app('db')->select("SELECT * FROM last_update");
        if (!$lastUpdate) {
            $lastUpdate = Carbon::now();
        } else {
            $lastUpdate = Carbon::createFromTimestamp($lastUpdate[0]->updated_at);
        }

        foreach ($events['item'] as $i => $event) {
            if ($i == 0 || Carbon::parse($event["date"])->setTimezone("Asia/Tehran") < $lastUpdate) {
                continue;
		    }
            $lat = explode(" ", $event["lat"])[0];
            $long = explode(" ", $event["long"])[0];
            $message = sprintf(
                "Region: %s%%0AMagnitude: %s%%0ADepth: %s%%0ATime: %s%%0ALocation: https://www.google.com/maps/place/%f,%f/@%f,%f,10z",
                $event["reg1"],
                $event["mag"],
                $event["dep"],
                Carbon::parse($event["date"])->setTimezone("Asia/Tehran")->format("Y-m-d H:i:s"),
                $lat,
                $long,
                $lat,
                $long
            );
            $response = Http::get("https://api.telegram.org/bot1138407370:AAGcehBntpDFAD8fOsRiOf-iLOV3oV0ovJI/sendMessage?chat_id=@IranianEarthquakes&text=" . $message);
            if ($response->status() == 200) {
                app('db')->update("update last_update set updated_at = ?", [Carbon::parse($event["date"])]);
            }
        }
    }
}
