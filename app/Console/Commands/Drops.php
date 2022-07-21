<?php

namespace App\Console\Commands;

use App\Helpers\Constants;
use App\Models\Drop;
use App\Services\MessageService;
use App\Services\OnChainService;
use App\Services\ThetaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Drops extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theta:drops';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update drops';

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
    public function handle()
    {
        $thetaService = resolve(ThetaService::class);
        $messageService = resolve(MessageService::class);

        $response = Http::get(Constants::DROP_API_URL . '/sale_order/list_archived?number=100&page=1&&expand=content_id&expand=nft_id&expand=buyer_id&expand=buyer_id.tps_id&expand=user_id&expand=user_id.tps_id&expand=sale_data_id');
        if (!$response->ok()) {
            Log::channel('db')->error('Request failed: thetadrop/sale_order/list_archived');
            return false;
        }

        $oldDropIds = Cache::get('old_drop_ids', []);
        $newDropIds = [];

        $data = [];
        $drops = $response->json()['body'];

        $saleDatas = [];
        $saleOrders = [];
        $packs = [];
        $types = [];
        $tps = [];
        $users = [];
        $nfts = [];

        foreach ($drops['sale_datas'] as $each) {
            $saleDatas[$each['sale_order_id']] = $each;
        }
        foreach ($drops['sale_orders'] as $each) {
            $saleOrders[$each['id']] = $each;
        }
        foreach ($drops['packs'] as $each) {
            $packs[$each['id']] = $each;
        }
        foreach ($drops['types'] as $each) {
            $types[$each['id']] = $each;
        }
        foreach ($drops['tps_profiles'] as $each) {
            $tps[$each['id']] = $each;
        }
        foreach ($drops['user_profiles'] as $each) {
            $users[$each['id']] = $each;
        }
        foreach ($drops['nfts'] as $each) {
            $nfts[$each['id']] = $each;
        }

        $data = [];
        foreach ($saleOrders as $id => $each) {
            $newDropIds[] = $id;
            if (in_array($id, $oldDropIds)) {
                continue;
            }

            $image = '';
            $name = '';
            if (isset($nfts[$each['nft_id']]) && !empty($nfts[$each['nft_id']]['name']) && !empty($nfts[$each['nft_id']]['image'])) {
                $image = $nfts[$each['nft_id']]['image'];
                $name = $nfts[$each['nft_id']]['name'];
            } else if (str_contains($each['content_id'], 'type_')) {
                $image = $types[$each['content_id']]['image'];
                $name = $types[$each['content_id']]['name'];
            } else if (str_contains($each['content_id'], 'pack_')) {
                $image = $packs[$each['content_id']]['image_url'];
                $name = $packs[$each['content_id']]['title'];
            }
            $sale = [
                'transaction_id' => $id,
                'buyer_username' => $tps[$users[$each['buyer_id']]['tps_id']]['username'],
                'buyer_displayname' => @$tps[$users[$each['buyer_id']]['tps_id']]['display_name'],
                'seller_username' => $tps[$users[$each['user_id']]['tps_id']]['username'],
                'seller_displayname' => @$tps[$users[$each['user_id']]['tps_id']]['display_name'],
                'type' => $each['content_id'],
                'image' => $image,
                'name' => $name,
                'usd' => $each['price'],
                'tfuel' => $saleDatas[$id]['price'],
                'currency' => $saleDatas[$id]['currency'],
                'date' => date('Y-m-d H:i:s', strtotime($saleDatas[$id]['create_time']))
            ];
            $data[] = $sale;
        }

        if (!empty($data)) {
            Drop::whereDate('date', '<=', now()->subDays(Constants::DROP_LIFETIME_DAYS))->delete();
            foreach ($data as $each) {
                Drop::updateOrCreate(
                    ['transaction_id' => $each['transaction_id']],
                    $each
                );
            }
        }

        Cache::put('old_drop_ids', $newDropIds);
        $thetaService->setCommandTracker('Drops', 'last_run', time());
        $this->info('Done');
        return 0;
    }

}
