<?php

namespace App\Http\Controllers;

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
        return view('admin.register_product_page');
    }

    public function register_product(Request $request)
    {

        $request->validate([
            'product_name' => 'required',
            'product_price' => 'required',
            'product_image' => 'required',
        ]);

        $product = new Product();
        $user_id = Auth::user()->id;

        $product->user_id = $user_id;
        $product->product_name = $request->product_name;
        $product->product_price = $request->product_price;

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

        } elseif($event_id == "0") {

            return redirect()->back()->with("message", "出品する催事が選択されていません");

        }else {

            foreach ($product_ids as $product_id) {

                $completed_shuppin_product = product::Where('id', '=', $product_id)->first();

                $completed_shuppin_product->auction_status = 0;
                $completed_shuppin_product->auction_id = $event_id;
                
            }
        }

        $completed_shuppin_product->save();


        return redirect()->back()->with("message", "出品が完了しました");
    }
}
