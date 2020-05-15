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

        foreach ($events['item'] as $i => $event) {
            if ($i == 0) {
                continue;
            }
            if ($i > 1) {
                break;
		    }
            $message = sprintf(
                "Region: %s%%0ADepth: %s%%0ATime: %s%%0ALocation: https://www.google.com/maps?q=%s,%s",
                $event["reg1"],
                $event["dep"],
                Carbon::parse($event["date"])->setTimezone("Asia/Tehran")->format("Y-m-d H:i:s"),
                explode(" ", $event["lat"])[0],
                explode(" ", $event["long"])[0]
            );
            Http::get("https://api.telegram.org/bot1138407370:AAGcehBntpDFAD8fOsRiOf-iLOV3oV0ovJI/sendMessage?chat_id=@IranianEarthquakes&text=" . $message);
        }
    }
}
