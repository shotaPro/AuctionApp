<?php

namespace App\Console\Commands;

use App\Models\Auction_bid;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Auction_event;
use App\Models\Product;
use Carbon\Carbon;



class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'User added';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $current_data = new Carbon();
        $update_won_person = Auction_event::join('products', 'products.auction_id', 'auction_events.id')->Where('end_date', '<', $current_data::today())->Where('products.auction_status', '=', 0)->get();

        //最低落札価格に満たない時は、落札者(won_person)を確定しない
        foreach ($update_won_person as $person) {

            if ($person->highest_bid < $person->product_lowest_price) {

                Auction_event::join('products', 'products.auction_id', 'auction_events.id')->Where('end_date', '<', $current_data::today())->Where('products.auction_status', '=', 0)->update(['products.auction_status' => 1]);

            } else {

                if ($person->highest_bid_person != NULL) {

                      Product::Where('products.id', '=', $person->id)->update(['products.auction_status' => 1, 'products.won_person' => $person->highest_bid_person]);

                } else {

                    Auction_event::join('products', 'products.auction_id', 'auction_events.id')->Where('end_date', '<', $current_data::today())->Where('products.auction_status', '=', 0)->update(['products.auction_status' => 1]);

                }
            }

        }
        // $current_data = new Carbon();
        // Auction_event::join('products', 'products.auction_id', 'auction_events.id')->Where('end_date', '<', $current_data::today())->Where('products.auction_status', '=', 0)->update(['products.auction_status' => 1]);


        $this->info('success');
    }
}
