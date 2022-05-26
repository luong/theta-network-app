<?php

namespace App\Services;
use Noweh\TwitterApi\Client;

class TweetService
{

    public function tweet($tx) {
        $client = $this->getClient();
        $text = '';
        if ($tx['type'] == 'transfer') {
            $text = "{$tx['amount']} transferred from unknown wallet to unknown wallet https://explorer.thetatoken.org/txs/{$tx['id']}";
        } else if ($tx['type'] == 'stake') {
            $text = "{$tx['amount']} staked https://explorer.thetatoken.org/txs/{$tx['id']}";
        }
        $client->tweet()->performRequest('POST', ['text' => $text]);
    }

    private function getClient() {
        $settings = [
            'account_id' => '24379483',
            'consumer_key' => 'RgS0RclonF2zP2XHzhqLzZKBM',
            'consumer_secret' => 'VpwzWmTMNMpd2oQd9c7Znr0xsvbBSGxffTxqc7vBo5EC6Bw4py',
            'bearer_token' => 'AAAAAAAAAAAAAAAAAAAAAFsAdAEAAAAAzjadkvlRQPHHoaHzR1Q%2FtnE0s%2Fo%3DDMjkgL4wI1ekEQ71DbBSQrHJpHsqQyqx3Q3EeMnGIx7FZ19kRf',
            'access_token' => '1341024171048890369-DSAJVdRW8D5IoWIHVpRFQDLLymhVXc',
            'access_token_secret' => '149sBGibRjCR3FifO9CS192dY1gq6bomWMJPGiYZquRAF'
        ];
        return new Client($settings);
    }
}
