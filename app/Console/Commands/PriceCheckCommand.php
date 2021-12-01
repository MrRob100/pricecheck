<?php

namespace App\Console\Commands;

use App\Models\Pair;
use Illuminate\Console\Command;

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

        foreach($pairs as $pair) {
            $this->calc($pair->symbol1);
            $this->calc($pair->symbol2);
        }

        return Command::SUCCESS;
    }

    public function calc(string $symbol): void
    {
        $candles = json_decode(file_get_contents("https://api.binance.com/api/v3/klines?symbol={$symbol}USDT&interval=1m&limit=60"), true);
        $hChange = ( $candles[sizeof($candles) -1][4] - $candles[0][1]) * 100 / $candles[0][1];

        if ($hChange > env('THRESH') || - $hChange > env('THRESH')) {
            dump("$symbol changed $hChange % last hour");
        }
    }
}
