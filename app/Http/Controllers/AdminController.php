<?php

namespace App\Http\Controllers;

use App\Models\Auction_bid;
use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Models\Auction_event;
use App\Models\Product;
use App\Models\User;

use Illuminate\Support\Facades\Auth;


class AdminController extends Controller
{

    public function event_setting_page()
    {
        $event_all = Auction_event::all();
        return view('admin.event_setting_page', compact('event_all'));
    }


    public function event_setting(Request $request)
    {
        $event_info = new Auction_event();
        $event_info->event_name = $request->name;
        $event_info->start_date = $request->start_date;
        $event_info->end_date = $request->end_date;

        $event_info->save();

        return redirect()->back()->with('message', "催事が設定されました");
    }

    public function edit_event_page($id)
    {
        $edit_event_page = Auction_event::find($id);
        return view('admin.edit_event_page', compact('edit_event_page'));
    }

    public function event_edit(Request $request, $id)
    {
        $edit_event = Auction_event::find($id);
        $edit_event->event_name = $request->event_name;
        $edit_event->start_date = $request->start_date;
        $edit_event->end_date = $request->end_date;

        $edit_event->save();

        return redirect()->back()->with('message', '催事の更新に成功しました');
    }

    public function edit_delete($id)
    {
        $delete_event = Auction_event::find($id);
        $delete_event->delete();

        return redirect()->back();
    }

    public function register_product_page()
    {
        $shuppin_users = User::all();

        return view('admin.register_product_page', compact('shuppin_users'));
    }

    public function register_product(Request $request)
    {

        $request->validate([
            'shuppinn_user' => 'required',
            'product_name' => 'required',
            'product_price' => 'required',
            'product_lowest_price' => 'required',
            'product_image' => 'required',
        ]);

        $product = new Product();
        $user_id = $request->input('shuppinn_user');

        $product->user_id = $user_id;
        $product->product_name = $request->product_name;
        $product->product_price = $request->product_price;
        $product->product_lowest_price = $request->product_lowest_price;

        $image = $request->product_image;

        $imagename = time() . '.' . $image->getClientOriginalExtension();
        $request->product_image->move('product_picture', $imagename);
        $product->product_image = $imagename;

        $user = User::find($user_id);
        $user->register_product_flg = 1;

        $product->save();
        $user->save();

        return redirect()->back()->with("message", "商品登録が正常に完了しました");
    }

    public function shuppin_page()
    {
        $shuppin_user = User::Where("register_product_flg", "=", 1)->get();

        return view('admin.shuppin_page', compact('shuppin_user'));
    }

    public function search_shuppin_person(Request $request)
    {
        $id = $request->user_data;

        if ($id == "0") {

            return redirect()->back();
        } else {

            $product_by_seleted_person = Product::where('user_id', '=', $id)->where('auction_status', '=', NULL)->get();
        }

        $shuppin_user = User::Where("register_product_flg", "=", 1)->get();

        $current_date = new Carbon;
        $event_info = Auction_event::Where('end_date', '>=', $current_date::today())->get();

        return view('admin.shuppin_search_page', compact('product_by_seleted_person', 'shuppin_user', 'event_info'));
    }

    public function select_shuppin_product(Request $request)
    {
        $product_ids = $request->input('select_product');
        $event_id =  $request->event_id;

        if ($product_ids == null) {

            return redirect()->back()->with("message", "商品が選択されていません");
        } elseif ($event_id == "0") {

            return redirect()->back()->with("message", "出品する催事が選択されていません");
        } else {

            foreach ($product_ids as $product_id) {

                $completed_shuppin_product = product::Where('id', '=', $product_id)->first();

                $completed_shuppin_product->auction_status = 0;
                $completed_shuppin_product->auction_id = $event_id;
            }
        }

        $completed_shuppin_product->save();


        return redirect()->back()->with("message", "出品が完了しました");
    }

    public function bill_page()
    {
        $selected_bill_status = NULL;

        return view('admin.bill_page', compact('selected_bill_status'));
    }


    public function bill_search_event(Request $request)
    {
        $current_date = new Carbon;
        $selected_bill_status = $request->input('bill_status');

        if ($selected_bill_status == "") {

            return redirect()->back();
        }

        if ($selected_bill_status == 0) {

            $not_yet_bill_event_products = Auction_event::join('products', 'products.auction_id', 'auction_events.id')->Where('products.highest_bid_person', '!=', NULL)->Where('products.auction_status', '=', 1)->Where('auction_events.end_date', '<', $current_date::today())->get();
        } else if ($selected_bill_status == 1) {

            $not_yet_bill_event_products = Auction_event::join('products', 'products.auction_id', 'auction_events.id')->Where('products.highest_bid_person', '!=', NULL)->Where('products.auction_status', '=', 3)->Where('auction_events.end_date', '<', $current_date::today())->get();
        }


        if ($not_yet_bill_event_products->isEmpty()) {

            return view('admin.bill_page', compact('not_yet_bill_event_products', 'selected_bill_status'));
        }

        return view('admin.bill_page', compact('not_yet_bill_event_products', 'selected_bill_status'));
    }

    public function create_bill_invoice($id)
    {
        $event_array = [];

        $invoice_data = product::join('auction_events', 'auction_events.id', 'products.auction_id')->Where('products.highest_bid_person', '=', $id)->get();


        $filePath = '/Users/noguchishouta/Desktop/Project/AuctionApp/public';

        foreach ($invoice_data as $data) {

            Product::Where('auction_status', '=', 1)->Where('auction_id', '=', $data->auction_id)->Where('highest_bid_person', '=', $data->highest_bid_person)->update(['auction_status' => 3]);
            $data_array = [];
            $event_id = $data->auction_id;

            if (!file_exists($filePath . '/' . $event_id . '/' . $data->highest_bid_person)) {

                mkdir($filePath . '/' . $event_id . '/' . $data->highest_bid_person, 0777, true);
            }
        }

        file_put_contents($filePath . '/' . $event_id . '/' . $data->highest_bid_person . '/' . 'data', $invoice_data, FILE_APPEND | LOCK_EX);

        return redirect()->back()->with("success_message", "送信に成功しました");
    }

    public function create_invoice_pdf($id)
    {

        $rakusatu_data = [];
        $users = Product::Where('highest_bid_person', '!=', NULL)->Where('auction_id', '=', $id)->Where('auction_status', '=', 3)->get();
        $filePath = '/Users/noguchishouta/Desktop/Project/AuctionApp/public';

        foreach ($users as $user) {

            $rakusatu_data[] = file_get_contents($filePath . '/' . $id . '/' . $user->highest_bid_person . '/' . 'data');

            for ($i = 0; $i < count($rakusatu_data); $i++) {

                $json = mb_convert_encoding($rakusatu_data[$i], 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');

                $invoice_data = json_decode($json, true);
            }
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

        return view('admin.invoice_pdf_page', compact('invoice_data', 'total_amount', 'total_amount_tax'));
    }

    public function admin_negotiation_page()
    {
        $user_id = Auth::user()->id;
        $products = Product::Where('auction_status', '=', 1)->Where('user_id', '=', $user_id)->get();

        foreach ($products as $product) {

            $negotiation_products = Product::Where('auction_status', '=', 1)->Where('product_lowest_price', '>', $product->highest_bid)->Where('won_person', '=', 0)->get();
        }

        return view('admin.admin_negotiation_page', compact('negotiation_products'));
    }

    public function admin_suggest_price(Request $request)
    {

        $user_id = Auth::user()->id;
        $suggest_price = $request->price;
        $product_id = $request->product_id;

        $negotiation_product_data = Product::find($product_id);

        if($suggest_price == $negotiation_product_data["highest_bid"]){

            $negotiation_product_data["negotiation_status"] = 1;
            $negotiation_product_data->save();

            return redirect()->back();

        }else {

            $auction_bid = new Auction_bid();
            $auction_bid->bid_product_id = $product_id;
            $auction_bid->bidBy = $user_id;
            $auction_bid->bid_price = $suggest_price;
            $auction_bid->auction_id = $negotiation_product_data["auction_id"];
            $auction_bid->suggest_price_status = 1;
            $auction_bid->save();

            $negotiation_product_data["negotiation_status"] = 2;
            $negotiation_product_data->save();

            return redirect()->back();

        }

    }
}
