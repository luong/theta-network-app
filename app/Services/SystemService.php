<?php

namespace App\Services;

class SystemService
{

    public function checkCommandsRunning($commands) {
        $thetaService = resolve(ThetaService::class);
        $commandTrackers = $thetaService->getCommandTrackers();
        foreach ($commands as $command) {
            if (!isset($commandTrackers[$command])) {
                return false;
            }
            $lastDate = date('Y-m-d', strtotime($commandTrackers[$command]['last_run']));
            if ($lastDate != date('Y-m-d')) {
                return false;
            }
        }
        return true;
    }
}
