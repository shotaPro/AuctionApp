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
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Response;


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
        $event = Auction_event::find($id);
        $user_id = Auth::user()->id;
        $products = product::join('auction_events', 'auction_events.id', 'products.auction_id')->Where('products.auction_id', '=', $id)->select('products.id', 'product_image', 'product_name', 'product_price', 'bid_count', 'highest_bid_person')->get();
        return view('user.auction_page', compact('products', 'user_id', 'event'));
    }

    public function watchList()
    {
        $user_id = Auth::user()->id;
        $products = product::join('likes', 'likes.likeOn', 'products.id')->Where('likes.likeBy', '=', $user_id)->get();

        return view('user.watchList', compact('products'));
    }

    public function already_bid()
    {

        $user_id = Auth::user()->id;
        $already_bid_product = Product::join('auction_bids', 'auction_bids.bid_product_id', 'products.id')->Where('auction_bids.bidBy', '=', $user_id)->select('products.id', 'product_image', 'product_name', 'product_price', 'bid_count', 'highest_bid_person')->get();

        return view('user.already_bid', compact('already_bid_product', 'user_id'));
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

    public function bid_product_detail($id)
    {
        $product = Product::find($id);
        return view('user.bid_product_detail', compact('product'));
    }



    public function change_product_order(Request $request, $id)
    {
        $selected_order = $request->input('select_order_choice');
        $event = Auction_event::find($id);
        $user_id = Auth::user()->id;

        switch ($selected_order) {
            case 0:
                return redirect()->back();
                break;
            case 1:
                $products = product::join('auction_events', 'auction_events.id', 'products.auction_id')->Where('products.auction_id', '=', $id)->orderBy('products.product_price', 'DESC')->select('products.id', 'product_image', 'product_name', 'product_price', 'bid_count', 'highest_bid_person')->get();
                break;
            case 2:
                $products = product::join('auction_events', 'auction_events.id', 'products.auction_id')->Where('products.auction_id', '=', $id)->orderBy('products.product_price', 'ASC')->select('products.id', 'product_image', 'product_name', 'product_price', 'bid_count', 'highest_bid_person')->get();
                break;
            case 3:
                $products = product::join('auction_events', 'auction_events.id', 'products.auction_id')->Where('products.auction_id', '=', $id)->orderBy('products.bid_count', 'DESC')->select('products.id', 'product_image', 'product_name', 'product_price', 'bid_count', 'highest_bid_person')->get();
                break;
            case 4:
                $products = product::join('auction_events', 'auction_events.id', 'products.auction_id')->Where('products.auction_id', '=', $id)->orderBy('products.bid_count', 'ASC')->select('products.id', 'product_image', 'product_name', 'product_price', 'bid_count', 'highest_bid_person')->get();
                break;
        }

        return view('user.auction_page', compact('products', 'user_id', 'event'));
    }

    public function invoice_page()
    {
        $user_id = Auth::user()->id;
        $current_date = new Carbon;
        $rakusatu_infos = product::join('auction_events', 'products.auction_id', 'auction_events.id')->Where('products.highest_bid_person', '=', $user_id)->Where('auction_status', '=', 3)->Where('auction_events.end_date', '<', $current_date)->get();
        return view('user.invoice_page', compact('rakusatu_infos'));
    }

    public function create_invoice_pdf_for_user($id)
    {
        $user_id = Auth::user()->id;
        $rakusatu_data = [];
        $filePath = '/Users/noguchishouta/Desktop/Project/AuctionApp/public';


        $rakusatu_data[] = file_get_contents($filePath . '/' . $id . '/' . $user_id . '/' . 'data');

        for ($i = 0; $i < count($rakusatu_data); $i++) {

            $json = mb_convert_encoding($rakusatu_data[$i], 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');

            $invoice_data = json_decode($json, true);
        }

        $total_amount = 0;
        $total_amount_tax = 0;

        foreach ($invoice_data as $data) {
            $total_amount += $data['highest_bid'];
        }

        foreach ($invoice_data as $data) {

            $total_amount_tax += $data['highest_bid'];
        }

        $total_amount_tax = $total_amount_tax * 1.1;

        return view('user.invoice_pdf_page', compact('invoice_data', 'total_amount', 'total_amount_tax'));
    }

    public function create_csv_for_user($id)
    {

        $user_id = Auth::user()->id;
        $rakusatu_data = [];
        $filePath = '/Users/noguchishouta/Desktop/Project/AuctionApp/public';
        $rakusatu_data[] = file_get_contents($filePath . '/' . $id . '/' . $user_id . '/' . 'data');

        for ($i = 0; $i < count($rakusatu_data); $i++) {

            $json = mb_convert_encoding($rakusatu_data[$i], 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');

            $invoice_data = json_decode($json, true);
        }

        $stream = fopen('php://temp', 'w');

        $csv_head = array('お客様名', 'オークション名', '出品者', '商品名', '落札金額');
        fputcsv($stream, $csv_head);

        foreach ($invoice_data as $data) {

            $array_data = array(
                'お客様名' => $data['highest_bid_person'],
                'オークション名' => $data['event_name'],
                '出品者' => $data['user_id'],
                '商品名' => $data['product_name'],
                '落札金額' => $data['highest_bid']
            );

            fputcsv($stream, $array_data);
        }

        rewind($stream);
        $csv = stream_get_contents($stream);
        $csv = mb_convert_encoding($csv, 'sjis-win', 'UTF-8');

        fclose($stream);

        $headers = array(                     //ヘッダー情報を指定する
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=test.csv'
        );

        return Response::make($csv, 200, $headers);   //ファイルをダウンロードする

    }


    public function user_negotiation_page()
    {
        $negotiation_products = NULL;
        $suggested_prices = NULL;

        $user_id = Auth::user()->id;
        $products = Product::Where('auction_status', '=', 1)->Where('highest_bid_person', '=', $user_id)->get();

        foreach ($products as $product) {

            $negotiation_products = Product::Where('auction_status', '=', 1)->Where('product_lowest_price', '>', $product->highest_bid)->Where('won_person', '=', 0)->get();
            $suggested_prices = Auction_bid::Where('auction_id', '=', $product->auction_id)->Where('suggest_price_status', '=', 1)->get();
        }

        if ($negotiation_products != NULL &&  $suggested_prices != NULL) {

            return view('user.user_negotiation_page', compact('negotiation_products', 'suggested_prices'));
        } else {

            return view('user.user_negotiation_page', compact('negotiation_products', 'suggested_prices'));
        }
    }

    public function reject_suggest_price($id)
    {

        $reject_suggest_price_product = Product::find($id);
        $reject_suggest_price_product["negotiation_status"] = 3;
        $reject_suggest_price_product->save();

        return redirect()->back();
    }

    public function accept_suggested_price(Request $request)
    {
        $product_id = $request->product_id;
        $suggested_price = $request->suggested_price;

        $product_data = Product::find($product_id);

        $product_data["negotiation_status"] = 1;
        $product_data["won_person"] = $product_data["highest_bid_person"];
        $product_data["highest_bid"] = $suggested_price;

        $product_data->save();

        return redirect()->back();
    }


    //入札関係の処理
    public function bid_product(Request $request, $id)
    {
        $bid_info = new Auction_bid();
        $bid_product = Product::find($id);
        $user_id = Auth::user()->id;
        $user_bid_max_price = "";

        //ユーザーがその商品に対して入札した全ての中で最も大きい額を取得する処理を記述
        $nyusatuALlInfo_to_the_product = Auction_bid::join('products', 'products.id', 'auction_bids.bid_product_id')->Where('bid_product_id', '=', $bid_product->id)->Where('auction_bids.bidBy', '=', $user_id)->Where('bid_price', Auction_bid::max('bid_price'))->first();

        if ($bid_product->highest_bid_person != NULL) {

            if ($nyusatuALlInfo_to_the_product == NULL) {

                $nyusatuALlInfo_to_the_product = Auction_bid::join('products', 'products.id', 'auction_bids.bid_product_id')->Where('bid_product_id', '=', $bid_product->id)->Where('auction_bids.bidBy', '=', $user_id)->select('bid_price')->get();

                $array = array();

                foreach ($nyusatuALlInfo_to_the_product as $a) {

                    $array[] =  $a->bid_price;
                }

                if (!empty($array)) {

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
            if ($bid_product->highest_bid_person != $user_id && $bid_product->highest_bid > $request->bid_price) {

                $bid_product->product_price = (int)$bid_info->bid_price + 500;

                $bid_product->save();

                return redirect()->back()->with("error", '最高入札者による自動入札が行われました。');
            } else if ($bid_info->bidBy == $user_id && $bid_product->highest_bid > $request->bid_price && $bid_product->highest_bid_person == $user_id) {

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

    public function ikkatu_bid(Request $request)
    {
        $product_id = explode(",",$request->product_id);
        $ikkatu_bid = explode(",", $request->ikkatu_bid);
        $event_id = $request->event_id;
        $user_id = Auth::user()->id;

        $error_message = array();

        dd($ikkatu_bid);


        if($ikkatu_bid != ""){

            foreach($product_id as $id){

                // Product::Where('auction_status', '=', 1)->Where('auction_id', '=', $event_id)->Where('highest_bid_person', '=', $user_id)->update(['highest_bid' => ]);
            }

        }


        $event = Auction_event::find($event_id);
        $products = product::join('auction_events', 'auction_events.id', 'products.auction_id')->Where('products.auction_id', '=', $event_id)->select('products.id', 'product_image', 'product_name', 'product_price', 'bid_count', 'highest_bid_person')->get();
        return view('user.auction_page', compact('products', 'user_id', 'event', 'error_message'));



        $event = Auction_event::find($id);
        $user_id = Auth::user()->id;
        $products = product::join('auction_events', 'auction_events.id', 'products.auction_id')->Where('products.auction_id', '=', $id)->select('products.id', 'product_image', 'product_name', 'product_price', 'bid_count', 'highest_bid_person')->get();
        return view('user.auction_page', compact('products', 'user_id', 'event'));
    }
}
