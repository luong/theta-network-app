<?php

namespace App\Services;
use App\Helpers\Constants;
use App\Helpers\Helper;
use Illuminate\Support\Facades\App;
use Noweh\TwitterApi\Client;

class MessageService
{

    public function hasLargeTransaction($tx) {
        if (!$this->canPost()) {
            return false;
        }
        $text = '';
        if ($tx['type'] == 'transfer') {
            $text = "[Bot] {$tx['amount']} transferred from unknown wallet " . Helper::makeThetaTransactionURL($tx['id']);
        } else if ($tx['type'] == 'stake') {
            $text = "[Bot] {$tx['amount']} staked " . Helper::makeThetaTransactionURL($tx['id']);
        }
        if (!empty($text)) {
            return $this->tweetText($text);
        }
        return false;
    }

    public function hasNewValidator($address, $amount) {
        if (!$this->canPost()) {
            return false;
        }
        $tweet = "[Bot] We're thrilled to have a new validator joining @Theta_Network : {$amount} \$THETA => " . Helper::makeThetaAccountURL($address);
        return $this->tweetText($tweet);
    }

    public function validatorChangesStakes($address, $oldAmount, $newAmount) {
        if (!$this->canPost()) {
            return false;
        }
        $tweet = "[Bot] A validator updated its \$THETA amount from {$oldAmount} to {$newAmount} => " . Helper::makeThetaAccountURL($address);
        return $this->tweetText($tweet);
    }

    public function sendDailyUpdates($params) {
        if (!$this->canPost()) {
            return false;
        }
        $tweet = "[Bot] Daily Updates @Theta_Network: \n - BTC: {$params['btcPrice']} \n - THETA: {$params['thetaPrice']} \n - TFUEL: {$params['tfuelPrice']} \n - TDROP: {$params['tdropPrice']} \n - THETA-TFUEL Ratio: {$params['ratio']} \n - THETA-TFUEL Stakes: {$params['thetaStakes']} - {$params['tfuelStakes']} \n";
        return $this->tweetText($tweet);
    }

    public function tweetText($text) {
        $client = $this->getTwitterClient();
        return $client->tweet()->performRequest('POST', ['text' => $text]);
    }

    private function canPost() {
        return App::environment('production');
    }

    private function getTwitterClient() {
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
