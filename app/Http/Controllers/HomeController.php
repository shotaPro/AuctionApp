<?php

namespace App\Http\Controllers;

use App\Models\Auction_event;
use App\Models\Product;
use App\Models\Like;
use App\Models\User;
use App\Models\Auction_bid;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class HomeController extends Controller
{


    public function index()
    {

        if (Auth::id()) {

            if (Auth::user()->admin_flg != 1) {

                $current_date = new Carbon;

                $events = Auction_event::where('end_date', '>=', $current_date::today())->get();

                return view('user.index', compact('events'));
            } else {

                return view('admin.index');
            }
        } else {

            return view('index');
        }
    }

    public function auction_page($id)
    {

        $products = product::join('auction_events', 'auction_events.id', 'products.auction_id')->Where('products.auction_id', '=', $id)->select('products.id', 'product_image', 'product_name', 'product_price')->get();
        return view('user.auction_page', compact('products'));
    }

    public function watchList()
    {
        $user_id = Auth::user()->id;
        $products = product::join('likes', 'likes.likeOn', 'products.id')->Where('likes.likeBy', '=', $user_id)->get();

        return view('user.watchList', compact('products'));
    }

    public function like($id)
    {
        $user_id = Auth::user()->id;
        $like = new Like();

        $like->likeBy = $user_id;
        $like->likeOn = $id;

        dd($like);

        $like->save();

        return redirect()->back();
    }

    public function unlike($id)
    {
        $user_id = Auth::user()->id;

        $deleteLike = Like::where('likeBy', '=', $user_id)->Where('likeOn', '=', $id)->first();
        $deleteLike->delete();

        return redirect()->back();
    }

    public function bid_product(Request $request, $id)
    {
        $bid_info = new Auction_bid();
        $bid_product = Product::find($id);
        $user_id = Auth::user()->id;

        // 500円単位でのみ入札できるようにする
        $an = $request->bid_price / 500;

        if(is_int($an)){

            dd("success");

        }else {

            dd("fail");

        }

        //bid_product table
        if($request->bid_price != NUll){

            $bid_info->bid_price = $request->bid_price;
            $bid_info->bid_product_id = $id;
            $bid_info->bidBy = $user_id;
            $bid_info->auction_id = $bid_product->auction_id;

        }else {

            return redirect()->back()->with("error", "商品金額が入力されていません");

        }


        //product table

        if($bid_product->highest_bid != NULL){

        }else {

        }

        if ($bid_info->bid_info != null) {

            $bid_info->bid_count += 1;

        }else {

            $bid_product->bid_count = 1;

        }

        $bid_product->save();
        $bid_info->save();


        return redirect()->back()->with("message", "入札が正常に完了しました");
    }
}
