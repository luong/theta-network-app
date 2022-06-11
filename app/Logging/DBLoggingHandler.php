<?php

namespace App\Logging;

use App\Helpers\Constants;
use App\Models\Log;
use Monolog\Handler\AbstractProcessingHandler;

class DBLoggingHandler extends AbstractProcessingHandler
{

    public function __construct()
    {
    }

    protected function write(array $record): void
    {
        Log::whereDate('created_at', '<=', now()->subDays(Constants::LOG_LIFETIME_DAYS))->delete();
        Log::create([
            'level' => $record['level_name'],
            'message' => $record['message']
        ]);
    }
}
