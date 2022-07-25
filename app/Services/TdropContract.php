<?php

namespace App\Services;

use App\Helpers\Constants;
use Web3\Contract;

class TdropContract
{
    const ABI = '[{"inputs":[{"internalType":"string","name":"name_","type":"string"},{"internalType":"string","name":"symbol_","type":"string"},{"internalType":"uint8","name":"decimals_","type":"uint8"},{"internalType":"uint256","name":"initialSupply_","type":"uint256"},{"internalType":"bool","name":"mintable_","type":"bool"}],"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"owner","type":"address"},{"indexed":true,"internalType":"address","name":"spender","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Approval","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"from","type":"address"},{"indexed":true,"internalType":"address","name":"to","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Transfer","type":"event"},{"inputs":[{"internalType":"address","name":"owner","type":"address"},{"internalType":"address","name":"spender","type":"address"}],"name":"allowance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"approve","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"account","type":"address"}],"name":"balanceOf","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"decimals","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"mint","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"mintable","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"name","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"symbol","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"totalSupply","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"stakeRewardAccumulated","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transfer","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"sender","type":"address"},{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transferFrom","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"}]';
    const STAKING_ABI = '[{"inputs":[],"name":"totalShares","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"}]';

    public function getTotalSypply() {
        $contract = new Contract(Constants::WEB3_RPC, self::ABI);
        $totalSupply = false;
        $contract->at(Constants::TDROP_CONTRACT_ID)->call('totalSupply', [], function ($err, $result) use (&$totalSupply) {
            $totalSupply = (float)$result[0]->toString();
        });
        return $totalSupply;
    }

    public function stakesRewarded() {
        $contract = new Contract(Constants::WEB3_RPC, self::ABI);
        $totalSupply = false;
        $contract->at(Constants::TDROP_CONTRACT_ID)->call('stakeRewardAccumulated', [], function ($err, $result) use (&$totalSupply) {
            $totalSupply = (float)$result[0]->toString() / Constants::THETA_WEI;
        });
        return $totalSupply;
    }

    public function getBalance($address) {
        $contract = new Contract(Constants::WEB3_RPC, self::ABI);
        $balance = false;
        $contract->at(Constants::TDROP_CONTRACT_ID)->call('balanceOf', $address, function ($err, $result) use (&$balance) {
            $balance = (float)$result[0]->toString() / Constants::THETA_WEI;
        });
        return $balance;
    }

    public function getStakingTotalShares() {
        $contract = new Contract(Constants::WEB3_RPC, self::STAKING_ABI);
        $totalShares = false;
        $contract->at(Constants::TDROP_STAKING_ADDRESS)->call('totalShares', [], function ($err, $result) use (&$totalShares) {
            $totalShares = (float)$result[0]->toString() / Constants::THETA_WEI;
        });
        return $totalShares;
    }

}
