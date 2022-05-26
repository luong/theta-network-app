<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Noweh\TwitterApi\Client;

class TweetBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theta:tweet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $settings = [
            'account_id' => '24379483',
            'consumer_key' => 'RgS0RclonF2zP2XHzhqLzZKBM',
            'consumer_secret' => 'VpwzWmTMNMpd2oQd9c7Znr0xsvbBSGxffTxqc7vBo5EC6Bw4py',
            'bearer_token' => 'AAAAAAAAAAAAAAAAAAAAAFsAdAEAAAAAzjadkvlRQPHHoaHzR1Q%2FtnE0s%2Fo%3DDMjkgL4wI1ekEQ71DbBSQrHJpHsqQyqx3Q3EeMnGIx7FZ19kRf',
            'access_token' => '1341024171048890369-DSAJVdRW8D5IoWIHVpRFQDLLymhVXc',
            'access_token_secret' => '149sBGibRjCR3FifO9CS192dY1gq6bomWMJPGiYZquRAF'
        ];

        $client = new Client($settings);
        $return = $client->tweet()->performRequest('POST', ['text' => 'This is a test']);
        dd($return);

        return 0;
    }
}
