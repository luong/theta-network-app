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
            $fromTo = 'from unknown wallet';
            if (isset($accounts[$tx['from']]) && isset($accounts[$tx['to']])) {
                $fromTo = 'from ' . $accounts[$tx['from']]['name'] . ' to ' . $accounts[$tx['to']]['name'];
            } else if (isset($accounts[$tx['from']])) {
                $fromTo = 'from ' . $accounts[$tx['from']]['name'];
            } else if (isset($accounts[$tx['to']])) {
                $fromTo = 'to ' . $accounts[$tx['to']]['name'];
            }
            $stakeAs = !empty($tx['node']) ? ('staked as ' . $tx['node']) : 'staked';
            $text = "{$tx['amount']} {$stakeAs} {$fromTo} " . Helper::makeSiteTransactionURL($tx['id']);
        } else if ($tx['type'] == 'unstake') {
            $fromTo = 'from unknown wallet';
            if (isset($accounts[$tx['from']]) && isset($accounts[$tx['to']])) {
                $fromTo = 'from ' . $accounts[$tx['from']]['name'] . ' to ' . $accounts[$tx['to']]['name'];
            } else if (isset($accounts[$tx['from']])) {
                $fromTo = 'from ' . $accounts[$tx['from']]['name'];
            } else if (isset($accounts[$tx['to']])) {
                $fromTo = 'to ' . $accounts[$tx['to']]['name'];
            }
            $text = "{$tx['amount']} unstaked {$fromTo} " . Helper::makeSiteTransactionURL($tx['id']);
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

    public function sendDailyUpdates($params)
    {
        $tweet = "Daily Updates: \n* TVL: {$params['tvl']} \n* \$theta: {$params['thetaPrice']} \n* \$tfuel: {$params['tfuelPrice']} \n* \$tdrop: {$params['tdropPrice']} \n* \$theta \$tfuel ratio: {$params['ratio']} \n* \$theta stake: {$params['thetaStakes']} \n* \$tfuel stake: {$params['tfuelStakes']} \n* \$tfuel supply: {$params['tfuelSupply']} \n";
        return $this->tweetText($tweet);
    }

    public function thankForDonation($tx)
    {
        $tweet = "Thanks for donation: {$tx['amount']} => " . Helper::makeSiteTransactionURL($tx['id']);
        return $this->tweetText($tweet);
    }

    public function hasLargeNFT($tx)
    {
        $amount = Helper::formatPrice($tx['usd'], 2);
        if ($tx['currency'] == 'tfuel') {
            $amount = Helper::formatNumber($tx['tfuel'], 2) . ' $tfuel (' . $amount . ')';
        }
        $tweet = "NFT [{$tx['name']}] sold for {$amount} => " . Helper::makeDropOrderURL($tx['transaction_id']);
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

    public function hasNews($newsUrl)
    {
        $tweet = "#THETA news: {$newsUrl}";
        return $this->tweetText($tweet);
    }
}
