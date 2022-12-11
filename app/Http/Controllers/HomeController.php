<?php

namespace App\Http\Controllers;

use App\Models\Auction_event;
use App\Models\Product;
use App\Models\Like;
use App\Models\User;
use App\Models\Auction_bid;

use Carbon\Carbon;
use Illuminate\Auth\Middleware\Authorize;
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

        $products = product::join('auction_events', 'auction_events.id', 'products.auction_id')->Where('products.auction_id', '=', $id)->select('products.id', 'product_image', 'product_name', 'product_price', 'bid_count')->get();
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
        $user_bid_max_price = "";

        //ユーザーがその商品に対して入札した全ての中で最も大きい額を取得する処理を記述
        $nyusatuALlInfo_to_the_product = Auction_bid::join('products', 'products.id', 'auction_bids.bid_product_id')->Where('bid_product_id', '=', $bid_product->id)->Where('auction_bids.bidBy', '=', $user_id)->Where('bid_price', Auction_bid::max('bid_price'))->first();

        if($bid_product->highest_bid_person != NULL){

            if($nyusatuALlInfo_to_the_product == NULL){

                $nyusatuALlInfo_to_the_product = Auction_bid::join('products', 'products.id', 'auction_bids.bid_product_id')->Where('bid_product_id', '=', $bid_product->id)->Where('auction_bids.bidBy', '=', $user_id)->select('bid_price')->get();

                $array= array();

                foreach ($nyusatuALlInfo_to_the_product as $a){

                    $array[] =  $a->bid_price;

                }

                if(!empty($array)){

                    $user_bid_max_price = max($array);

                }


            }
        }


        ///////////////////////////////////////////////////////////////
        //insert bid_product table
        ////////////////////////////////////////////////////////////////


        // 入札単位のバリデーション
        if ($request->bid_price != NUll) {

            // 500円単位でのみ入札できるようにする
            if (is_int($request->bid_price / 500)) {

                $bid_info->bid_price = $request->bid_price;
                $bid_info->bid_product_id = $id;
                $bid_info->bidBy = $user_id;
                $bid_info->auction_id = $bid_product->auction_id;
            } else {

                return redirect()->back()->with("error", "500円単位で入札してください");
            }
        } else {

            return redirect()->back()->with("error", "商品金額が入力されていません");
        }

        //入札件数プラス
        if ($bid_product->bid_count != 0) {

            $bid_product->bid_count += 1;
        } else {

            $bid_product->bid_count = 1;
        }

        $bid_info->save();

        ////////////////////////////////////////////////////////////////
        //update products
        /////////////////////////////////

        //二度目の入札の場合、過去の入札の額と比較する
        if ($nyusatuALlInfo_to_the_product != NULL || $user_bid_max_price != "") {

            //自動入札
            if($bid_product->highest_bid_person != $user_id && $bid_product->highest_bid > $request->bid_price){

                $bid_product->product_price = (int)$bid_info->bid_price + 500;

                $bid_product->save();

                return redirect()->back()->with("error", '最高入札者による自動入札が行われました。');


            }else if ($bid_info->bidBy == $user_id && $bid_product->highest_bid > $request->bid_price && $bid_product->highest_bid_person == $user_id) {

                return redirect()->back()->with("error", "前回入札した金額より高い金額を入力してください。");

            } else {

                if ($bid_product->product_lowest_price > $request->bid_price) {

                    if ($bid_product->highest_bid_person == NULL) {

                        $bid_product->highest_bid_person = Auth::user()->id;

                        $bid_product->save();

                        return redirect()->back()->with("message", "入札が正常に完了しました。※お客様の金額は最低落札金額に達していません。現在の最高入札者はお客様です！");
                    } else {

                        return redirect()->back()->with("message", "入札が正常に完了しました。※お客様の金額は最低落札金額に達していません");
                    }

                } else if ($bid_product->highest_bid != NULL && $bid_product->highest_bid_person != NULL) {

                    if ($bid_product->highest_bid > $request->bid_price) {


                        return redirect()->back()->with("message", "最高入札者権利を取得できませんでした。");

                    } else if ($bid_product->highest_bid == $request->bid_price) {

                        if ($bid_info->bidBy == $user_id && $bid_product->highest_bid_person == $user_id) {


                            return redirect()->back()->with("error", '最高入札金額を更新する場合はさらに高い金額を入札してください');
                        } else {

                            return redirect()->back()->with("error", "先に同額の入札者がいたため、最高入札者権利を取得できませんでした。");
                        }
                    } else {

                        if ($bid_product->highest_bid_person == $user_id) {

                            if ($bid_product->bid_price < $request->bid_price) {

                                $bid_product->highest_bid = $request->bid_price;

                                $bid_product->save();

                                return redirect()->back()->with("message", "最高入札金額を更新しました");
                            }
                        } else {

                            $bid_product->highest_bid = $request->bid_price;
                            $bid_product->highest_bid_person = Auth::user()->id;
                            $bid_product->product_price = (int)$bid_product->highest_bid + 500;

                            $bid_product->save();

                            return redirect()->back()->with("message", "おめでとうございます！お客様が最高入札権利を獲得しました。");
                        }
                    }
                } else {

                    $bid_product->highest_bid = $request->bid_price;

                    $bid_product->save();

                    return redirect()->back()->with("message", "入札が正常に完了しました 現在の最高入札者はお客様です");
                }
            }

            //一度目の入札の場合
        } else {

            if ($bid_product->product_lowest_price > $request->bid_price) {

                if ($bid_product->highest_bid_person == NULL) {

                    $bid_product->highest_bid_person = Auth::user()->id;

                    $bid_product->save();

                    return redirect()->back()->with("message", "入札が正常に完了しました。※お客様の金額は最低落札金額に達していません。現在の最高入札者はお客様です！");
                } else {

                    return redirect()->back()->with("message", "入札が正常に完了しました。※お客様の金額は最低落札金額に達していません");
                }
            } else if ($bid_product->highest_bid != NULL && $bid_product->highest_bid_person != NULL) {

                if ($bid_product->highest_bid > $request->bid_price) {


                    return redirect()->back()->with("message", "最高入札者権利を取得できませんでした。");
                } else if ($bid_product->highest_bid == $request->bid_price) {


                    return redirect()->back()->with("error", "先に同額の入札者がいたため、最高入札者権利を取得できませんでした。");
                } else {

                    $bid_product->highest_bid = $request->bid_price;
                    $bid_product->highest_bid_person = Auth::user()->id;

                    $bid_product->save();

                    return redirect()->back()->with("message", "おめでとうございます！お客様が最高入札権利を獲得しました。");
                }
            } else {

                $bid_product->highest_bid = $request->bid_price;
                $bid_product->highest_bid_person = Auth::user()->id;

                $bid_product->save();

                return redirect()->back()->with("message", "入札が正常に完了しました 現在の最高入札者はお客様です");
            }
        }
    }
}
