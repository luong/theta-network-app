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
        $accounts = resolve(ThetaService::class)->getAccounts();
        $text = '';
        if ($tx['type'] == 'transfer') {
            $fromTo = 'from unknown wallet';
            if (isset($accounts[$tx['from']]) && isset($accounts[$tx['to']])) {
                $fromTo = 'from ' . $accounts[$tx['from']]['name'] . ' to ' . $accounts[$tx['to']]['name'];
            } else if (isset($accounts[$tx['from']])) {
                $fromTo = 'from ' . $accounts[$tx['from']]['name'];
            } else if (isset($accounts[$tx['to']])) {
                $fromTo = 'to ' . $accounts[$tx['to']]['name'];
            }
            $text = "{$tx['amount']} transferred {$fromTo} " . Helper::makeSiteTransactionURL($tx['id']);
        } else if ($tx['type'] == 'stake') {
            $from = 'from unknown wallet';
            if (isset($accounts[$tx['from']])) {
                $from = 'from ' . $accounts[$tx['from']]['name'];
            }
            $purpose = !empty($tx['node']) && $tx['node'] == 'validator' ? 'staked as a validator' : 'staked';
            $text = "{$tx['amount']} {$purpose} {$from} " . Helper::makeSiteTransactionURL($tx['id']);
        } else if ($tx['type'] == 'withdraw') {
            $from = 'from unknown wallet';
            if (isset($accounts[$tx['from']])) {
                $from = 'from ' . $accounts[$tx['from']]['name'];
            }
            $text = "{$tx['amount']} withdrawn {$from} " . Helper::makeSiteAccountURL($tx['from']);
        }
        if (!empty($text)) {
            return $this->tweetText($text);
        }
        return false;
    }

    public function hasNewValidator($address, $amount)
    {
        $tweet = "We're thrilled to have a new validator joining @Theta_Network : {$amount} \$theta => " . Helper::makeSiteAccountURL($address);
        return $this->tweetText($tweet);
    }

    public function validatorChangesStakes($address, $oldAmount, $newAmount)
    {
        $accounts = resolve(ThetaService::class)->getAccounts();
        $accountName = 'A validator';
        if (isset($accounts[$address])) {
            $accountName = 'The validator ' .  $accounts[$address]['name'];
        }
        $tweet = "{$accountName} updated its \$theta amount from {$oldAmount} to {$newAmount} => " . Helper::makeSiteAccountURL($address);
        return $this->tweetText($tweet);
    }

    public function sendDailyUpdates($params)
    {
        $tweet = "Daily Updates: \n- TVL: {$params['tvl']} \n- \$theta: {$params['thetaPrice']} \n- \$tfuel: {$params['tfuelPrice']} \n- \$tdrop: {$params['tdropPrice']} \n- \$theta \$tfuel ratio: {$params['ratio']} \n- \$theta stake: {$params['thetaStakes']} \n- \$tfuel stake: {$params['tfuelStakes']} \n- \$tfuel supply: {$params['tfuelSupply']} \n";
        return $this->tweetText($tweet);
    }

    public function tweetText($text)
    {
        if (!$this->canPost()) {
            return false;
        }
        $client = $this->getTwitterClient();
        return $client->tweet()->performRequest('POST', ['text' =>'[Bot] ' .  $text]);
    }

    private function canPost()
    {
        return false; //App::environment('production');
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
