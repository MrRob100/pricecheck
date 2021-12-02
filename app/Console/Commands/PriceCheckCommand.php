<?php

namespace App\Console\Commands;

use App\Models\Pair;
use Illuminate\Console\Command;
use Twilio\Jwt\ClientToken;
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

        $symbols = [];

        foreach($pairs as $pair) {

            $res1 = $this->calc($pair->symbol1);
            $res2 = $this->calc($pair->symbol2);

            if ($res1) {
                array_push($symbols, $res1);
            }

            if ($res2) {
                array_push($symbols, $res2);
            }
        }

        $this->sendMessage(implode(' ', $symbols), env('THRESH'));

        return Command::SUCCESS;
    }

    public function calc(string $symbol): ?string
    {
        $candles = json_decode(file_get_contents("https://api.binance.com/api/v3/klines?symbol={$symbol}USDT&interval=1m&limit=60"), true);
        $hChange = ( $candles[sizeof($candles) -1][4] - $candles[0][1]) * 100 / $candles[0][1];

        if ($hChange > env('THRESH') || - $hChange > env('THRESH')) {
            return $symbol;
        } else {
            return null;
        }
    }

    public function sendMessage(string $symbols, int $change): void
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');

        $client = new Client($sid, $token);

        $client->messages->create(
            '+447849841646',
            array(
                'from' => '+447458195385',
                'body' => "$symbols changed > $change% last hour"
            )
        );
    }
}
