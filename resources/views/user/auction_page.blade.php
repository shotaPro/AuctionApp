<x-app-layout>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Document</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    </head>

    <body>

        <h2 class="text-center m-4">商品一覧</h2>
        @if (session()->has('message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert">
                    x
                </button>
                {{ session()->get('message') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert">
                    x
                </button>
                {{ session()->get('error') }}
            </div>
        @endif

        <div class="mb-3">
            <h2>検索</h2>
            <form action="{{ url('change_product_order', $event->id) }}" method="POST">
                @csrf
                <select name="select_order_choice" id="">
                    <option value="0">--</option>
                    <option value="1">価格の高い順</option>
                    <option value="2">価格の安い順</option>
                    <option value="3">入札が多い順</option>
                    <option value="4">入札が少ない順</option>
                </select>
                <button class="btn btn-primary" type="submit">絞り込む</button>
            </form>
        </div>

        <div class="row row-cols-1 row-cols-md-3 g-4">
            @foreach ($products as $product)
                @if($product->id == $product->id)

                <div class="col-3">
                    <div class="card">
                        @if ($product->highest_bid_person == $user_id)
                            <div style="color: red">最高入札者権利取得</div>
                        @endif
                        <img src="/product_picture/{{ $product->product_image }}" class="card-img-top" alt="...">
                        <div class="card-body">
                            <p class="card-title">商品名:
                                {{ $product->product_name }}</p>
                            <p class="card-text">現在価格: {{ $product->product_price }} 入札数：{{ $product->bid_count }}件</p>
                        </div>
                        <form class="mx-auto" action="{{ url('bid_product', $product->id) }}" method="POST">
                            @csrf
                            <input class="mb-2" style="width: 300px; margin: auto;" type="number" name="bid_price"
                                placeholder="金額を入力してください"><br>
                            <button style="width: 300px; margin: auto;" class="btn btn-primary mb-2">入札する</button>
                        </form>
                        <a href="{{ url('bid_product_detail', $product->id) }}" style="width: 300px; margin: auto;"
                            class="btn btn-success mb-2">詳細を見る</a>
                        @if (Auth::user()->likeProduct($product->id))
                            <a href="{{ url('unlike', $product->id) }}" style="width: 300px; margin: auto;"
                                class="btn btn-secondary mb-2">お気に入りを解除する</a>
                        @else
                            <a href="{{ url('like', $product->id) }}" style="width: 300px; margin: auto;"
                                class="btn btn-warning mb-2">お気に入りに登録</a>
                        @endif
                    </div>
                </div>
            @endif
            @endforeach
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
        </script>
    </body>


    </html>


</x-app-layout>
