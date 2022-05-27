<?php

namespace App\Services;
use App\Helpers\Constants;
use App\Helpers\Helper;
use Illuminate\Support\Facades\App;
use Noweh\TwitterApi\Client;

class MessageService
{

    public function hasLargeTransaction($tx)
    {
        if (!$this->canPost()) {
            return false;
        }
        $holders = resolve(ThetaService::class)->getHolders();
        $text = '';
        if ($tx['type'] == 'transfer') {
            $fromTo = 'from unknown wallet';
            if (isset($holders[$tx['from']]) && isset($holders[$tx['to']])) {
                $fromTo = 'from ' . $holders[$tx['from']]['name'] . ' to ' . $holders[$tx['to']]['name'];
            } else if (isset($holders[$tx['from']])) {
                $fromTo = 'from ' . $holders[$tx['from']]['name'];
            } else if (isset($holders[$tx['to']])) {
                $fromTo = 'to ' . $holders[$tx['to']]['name'];
            }
            $text = "{$tx['amount']} transferred {$fromTo} " . Helper::makeThetaTransactionURL($tx['id']);
        } else if ($tx['type'] == 'stake') {
            $from = 'from unknown wallet';
            if (isset($holders[$tx['from']])) {
                $from = 'from ' . $holders[$tx['from']]['name'];
            }
            $text = "{$tx['amount']} staked {$from} " . Helper::makeThetaTransactionURL($tx['id']);
        }
        if (!empty($text)) {
            return $this->tweetText($text);
        }
        return false;
    }

    public function hasNewValidator($address, $amount)
    {
        if (!$this->canPost()) {
            return false;
        }
        $tweet = "We're thrilled to have a new validator joining @Theta_Network : {$amount} \$THETA => " . Helper::makeThetaAccountURL($address);
        return $this->tweetText($tweet);
    }

    public function validatorChangesStakes($address, $oldAmount, $newAmount)
    {
        if (!$this->canPost()) {
            return false;
        }
        $holders = resolve(ThetaService::class)->getHolders();
        $accountName = 'A validator';
        if (isset($holders[$address])) {
            $accountName = 'The validator ' .  $holders[$address]['name'];
        }
        $tweet = "{$accountName} updated its \$THETA amount from {$oldAmount} to {$newAmount} => " . Helper::makeThetaAccountURL($address);
        return $this->tweetText($tweet);
    }

    public function sendDailyUpdates($params)
    {
        if (!$this->canPost()) {
            return false;
        }
        $tweet = "Daily Updates @Theta_Network: \n - BTC: {$params['btcPrice']} \n - THETA: {$params['thetaPrice']} \n - TFUEL: {$params['tfuelPrice']} \n - TDROP: {$params['tdropPrice']} \n - THETA-TFUEL Ratio: {$params['ratio']} \n - THETA-TFUEL Stakes: {$params['thetaStakes']} - {$params['tfuelStakes']} \n";
        return $this->tweetText($tweet);
    }

    public function tweetText($text)
    {
        $client = $this->getTwitterClient();
        return $client->tweet()->performRequest('POST', ['text' =>'[Bot] ' .  $text]);
    }

    private function canPost()
    {
        return App::environment('production');
    }

    private function getTwitterClient()
    {
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