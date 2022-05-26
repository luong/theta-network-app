<?php

namespace App\Services;
use App\Helpers\Constants;
use App\Helpers\Helper;
use Noweh\TwitterApi\Client;

class TweetService
{

    public function tweetTransaction($tx) {
        $client = $this->getClient();
        $text = '';
        if ($tx['type'] == 'transfer') {
            $text = "[Bot] {$tx['amount']} transferred from unknown wallet " . Helper::makeThetaTransactionURL($tx['id']);
        } else if ($tx['type'] == 'stake') {
            $text = "[Bot] {$tx['amount']} staked " . Helper::makeThetaTransactionURL($tx['id']);
        }
        if (!empty($text)) {
            return $client->tweet()->performRequest('POST', ['text' => $text]);
        }
        return false;
    }

    public function tweetText($text) {
        $client = $this->getClient();
        return $client->tweet()->performRequest('POST', ['text' => $text]);
    }

    private function getClient() {
        $settings = [
            'account_id' => Constants::TWITTER_PROJECT_ID,
            'consumer_key' => Constants::TWITTER_CONSUMER_KEY,
            'consumer_secret' => Constants::TWITTER_CONSUMER_SECRET,
            'bearer_token' => Constants::TWITTER_BEARER_TOKEN,
            'access_token' => Constants::TWITTER_ACCESS_TOKEN,
            'access_token_secret' => Constants::TWITTER_ACCESS_TOKEN_SECRET
        ];
        return new Client($settings);
    }
}
