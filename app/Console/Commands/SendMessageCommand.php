<?php


namespace App\Console\Commands;


use App\LastUpdate;
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
        $response = Http::get("http://irsc.ut.ac.ir/events_list_fa.xml");

        $xml = simplexml_load_string($response->body());
        $json = json_encode($xml);
        $events = json_decode($json,TRUE);

        foreach ($events['item'] as $i => $event) {
            $lastEvent = LastUpdate::query()->where('id', 1)->first();
            $lastEventId = $lastEvent ? $lastEvent->event_id : $events['item'][1]["id"];
            if ($i == 0 || $event["id"] < $lastEventId) {
                continue;
		    }
            $lat = explode(" ", $event["lat"])[0];
            $long = explode(" ", $event["long"])[0];
            $url = sprintf(
                "https://cutt.ly/api/api.php?key=e347c6fafd1f1c1565e8d93b268d081620df6&short=https://www.google.com/maps/place/%f,%f/@%f,%f,10z",
                $lat,
                $long,
                $lat,
                $long
            );
            $json = file_get_contents($url);
            $data = json_decode ($json, true);
            $mapUrl = $data["url"]["shortLink"] ?? $url;
            $message = sprintf(
                "گزارش مقدماتی زمین‌لرزه %%0A منطقه: %s%%0Aبزرگی: %s ریشتر%%0Aعمق: %s کیلومتر%%0Aزمان: %s%%0Aموقعیت مکانی: %s",
                $event["reg1"],
                $event["mag"],
                $event["dep"],
                $event["date"],
                $mapUrl
            );
            $response = Http::get("https://api.telegram.org/bot1138407370:AAGcehBntpDFAD8fOsRiOf-iLOV3oV0ovJI/sendMessage?chat_id=@IranianEarthquakes&text=" . $message);
            if ($response->status() == 200) {
                app('db')->table('last_update')->updateOrInsert(
                    ['id' => 1],
                    ['event_id' => (int)$event["id"]]
                );
            }
        }
    }
}
