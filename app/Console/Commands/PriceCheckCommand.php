<?php

namespace App\Console\Commands;

use App\Models\Pair;
use Illuminate\Console\Command;
use Twilio\Rest\Client;

class PriceCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:change';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Price Change';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $pairs = Pair::all();

        $info = [];

        foreach($pairs as $pair) {

            $res1 = $this->calc($pair->symbol1);
            $res2 = $this->calc($pair->symbol2);

            if ($res1) {
                array_push($info, $res1);
            }

            if ($res2) {
                array_push($info, $res2);
            }
        }

        if (sizeof($info) > 0) {
            $this->sendMessage(implode(' ', $info));
        }

        return Command::SUCCESS;
    }

    public function calc(string $symbol): ?string
    {
        $candles = json_decode(file_get_contents("https://api.binance.com/api/v3/klines?symbol={$symbol}USDT&interval=1m&limit=60"), true);
        $hChange = ($candles[sizeof($candles) -1][4] - $candles[0][1]) * 100 / $candles[0][1];

        if ($hChange > env('THRESH') || - $hChange > env('THRESH')) {
            $rounded = round($hChange, 1);
            return "$symbol $rounded%,";
        } else {
            return null;
        }
    }

    public function sendMessage(string $info): void
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $thresh = env('THRESH');
        $to = env('TO_NUMBER');

        $balance = $this->getBalance($sid, $token);

        $client = new Client($sid, $token);

        $client->messages->create(
            $to,
            [
                'from' => env('FROM_NUMBER'),
                'body' => "$info thresh: {$thresh}%, twilio balance: £$balance"
            ]
        );
    }

    public function getBalance($sid, $token): string
    {
        $curl = curl_init();

        $enc = base64_encode("$sid:$token");

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.twilio.com/2010-04-01/Accounts/$sid/Balance.json",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic $enc"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response)->balance;
    }
}
