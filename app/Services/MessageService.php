<?php

namespace App\Services;
use App\Helpers\Constants;
use App\Helpers\Helper;
use App\Mail\WalletRadarEmail;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Noweh\TwitterApi\Client;
use TwitterAPIExchange;

class MessageService
{

    public function notifyWalletChanges($transaction) {
        if ($transaction['usd'] < Constants::USER_WALLET_TRACK_AMOUNT) {
            return;
        }

        $wallets = resolve(ThetaService::class)->getWallets();

        if (isset($wallets[$transaction['from']])) {
            Mail::to($wallets[$transaction['from']])->send(new WalletRadarEmail(['to' => $wallets[$transaction['from']], 'account' => $transaction['from'], 'action' => $transaction['type'], 'amount' => $transaction['amount']]));
        }

        if (isset($wallets[$transaction['to']])) {
            Mail::to($wallets[$transaction['to']])->send(new WalletRadarEmail(['to' => $wallets[$transaction['to']], 'account' => $transaction['to'], 'action' => $transaction['type'], 'amount' => $transaction['amount']]));
        }
    }

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
            $text = "{$tx['amount']} transferred {$fromTo} " . Helper::makeSiteTransactionURL($tx['id'], $tx['currency']);
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
            $text = "{$tx['amount']} {$stakeAs} {$fromTo} " . Helper::makeSiteTransactionURL($tx['id'], $tx['currency']);
        } else if ($tx['type'] == 'unstake') {
            $fromTo = 'from unknown wallet';
            if (isset($accounts[$tx['from']]) && isset($accounts[$tx['to']])) {
                $fromTo = 'from ' . $accounts[$tx['from']]['name'] . ' to ' . $accounts[$tx['to']]['name'];
            } else if (isset($accounts[$tx['from']])) {
                $fromTo = 'from ' . $accounts[$tx['from']]['name'];
            } else if (isset($accounts[$tx['to']])) {
                $fromTo = 'to ' . $accounts[$tx['to']]['name'];
            }
            $text = "{$tx['amount']} unstaked {$fromTo} " . Helper::makeSiteTransactionURL($tx['id'], $tx['currency']);
        }
        if (!empty($text)) {
            return $this->tweetText($text);
        }
        return false;
    }

    public function hasNewValidator($address, $amount)
    {
        $tweet = "We're thrilled to have a new validator joining @Theta_Network : {$amount} \$theta " . Helper::makeSiteAccountURL($address);
        return $this->tweetText($tweet);
    }

    public function sendDailyUpdates($params)
    {
        $tweet = "Daily Updates: \n* TVL: {$params['tvl']} \n* \$theta: {$params['thetaPrice']} \n* \$tfuel: {$params['tfuelPrice']} \n* \$tdrop: {$params['tdropPrice']} \n* \$theta \$tfuel ratio: {$params['ratio']} \n* \$theta stake: {$params['thetaStakes']} \n* \$tfuel stake: {$params['tfuelStakes']} \n* \$tfuel supply: {$params['tfuelSupply']} \n";
        return $this->tweetText($tweet);
    }

    public function sendDailyUpdatesV2($filePath)
    {
        $uploadImageResult = $this->requestTwitterV1(
            'https://upload.twitter.com/1.1/media/upload.json',
            'POST',
            ['media_data' => base64_encode(file_get_contents($filePath))]
        );
        if (empty($uploadImageResult) || empty($uploadImageResult['media_id'])) {
            return false;
        }

        $params = [
            'text' => '#THETA daily updates ' . date('Y-m-d') . ' https://thetapizza.com',
            'media' => ['media_ids' => [$uploadImageResult['media_id_string']]]
        ];

        return $this->tweetV2($params);
    }

    public function thankForDonation($tx)
    {
        $tweet = "Thanks for donation: {$tx['amount']} " . Helper::makeSiteTransactionURL($tx['id']);
        return $this->tweetText($tweet);
    }

    public function hasLargeNFT($tx)
    {
        $amount = Helper::formatPrice($tx['usd'], 2);
        if ($tx['currency'] == 'tfuel') {
            $amount = Helper::formatNumber($tx['tfuel'], 2) . ' $tfuel (' . $amount . ')';
        }
        $tweet = "#THETA NFT \"{$tx['name']}\" sold for {$amount} " . Helper::makeDropOrderURL($tx['transaction_id']);

        $uploadImageResult = $this->requestTwitterV1(
            'https://upload.twitter.com/1.1/media/upload.json',
            'POST',
            ['media_data' => base64_encode(file_get_contents($tx['image']))]
        );
        if (empty($uploadImageResult) || empty($uploadImageResult['media_id'])) {
            return false;
        }

        $params = [
            'text' => $tweet,
            'media' => ['media_ids' => [$uploadImageResult['media_id_string']]]
        ];

        return $this->tweetV2($params);
    }

    public function tweetText($text)
    {
        if (!$this->canPost()) {
            return false;
        }
        $client = $this->getTwitterClient();
        return $client->tweet()->performRequest('POST', ['text' => '[Bot] ' .  $text]);
    }

    public function tweetV2($params)
    {
        if (!$this->canPost()) {
            return false;
        }
        $client = $this->getTwitterClient();
        $params['text'] = '[Bot] ' . $params['text'];
        return $client->tweet()->performRequest('POST', $params);
    }

    public function hasNews($newsUrl)
    {
        $tweet = "#THETA news {$newsUrl}";
        return $this->tweetText($tweet);
    }

    public function requestTwitterV1($endpoint, $method, $params)
    {
        $settings = array(
            'oauth_access_token' => Constants::TWITTER_ACCESS_TOKEN,
            'oauth_access_token_secret' => Constants::TWITTER_ACCESS_TOKEN_SECRET,
            'consumer_key' => Constants::TWITTER_CONSUMER_KEY,
            'consumer_secret' => Constants::TWITTER_CONSUMER_SECRET
        );
        $twitter = new TwitterAPIExchange($settings);
        return json_decode($twitter->buildOauth($endpoint, $method)->setPostfields($params)->performRequest(), 1);
    }

    public function canPost()
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
