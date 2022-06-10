<?php

namespace App\Logging;

use App\Models\Log;
use Monolog\Handler\AbstractProcessingHandler;

class DBLoggingHandler extends AbstractProcessingHandler
{

    public function __construct()
    {
    }

    protected function write(array $record): void
    {
        Log::whereDate('created_at', '<=', now()->subDays(7))->delete();
        Log::create([
            'level' => $record['level_name'],
            'message' => $record['message']
        ]);
    }
}
