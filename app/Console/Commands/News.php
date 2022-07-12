<?php

namespace App\Console\Commands;

use App\Helpers\Constants;
use App\Models\Setting;
use App\Services\MessageService;
use App\Services\ThetaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class News extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theta:news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update news';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ThetaService $thetaService, MessageService $messageService)
    {
        // Check articles
        $response = Http::get(Constants::THETA_MARKETING_API_URL . '/v1/news');
        if (!$response->ok()) {
            Log::channel('db')->error('Request failed: marketing/v1/news');
            return false;
        }
        $newsUrl = $response->json()[0]['href'];
        $newsObj = Setting::where('code', 'latest_news')->first();
        if (!empty($newsObj) && !empty($newsUrl) && $newsObj->value != $newsUrl) {
            $newsObj->value = $newsUrl;
            $newsObj->save();
            $messageService->hasNews($newsUrl);
        }

        $thetaService->cacheSettings();
        $thetaService->setCommandTracker('News', 'last_run', time());
        $this->info('Done');
        return 0;
    }
}
