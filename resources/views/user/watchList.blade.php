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
        <link rel="stylesheet" href="admin/assets/css/event.css">
    </head>

    <body>

        <h2 class="text-center m-4">お気に入り商品一覧</h2>

        @foreach($products as $product)
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col">
                <div class="card">
                    <img src="/product_picture/{{ $product->product_image }}" class="card-img-top" alt="...">
                    <div class="card-body">
                        <p class="card-title">商品名: {{ $product->product_name }}</p>
                        <p class="card-text">価格: {{ $product->product_price }}</p>
                    </div>
                    <a style="width: 300px; margin: auto;" class="btn btn-primary mb-2">入札する</a>
                    <a style="width: 300px; margin: auto;" class="btn btn-success mb-2">詳細を見る</a>
                    @if(Auth::user()->likeProduct($product->likeOn))
                    <a href="{{ url('unlike', $product->likeOn) }}" style="width: 300px; margin: auto;" class="btn btn-secondary mb-2">お気に入りを解除する</a>
                    @else
                    <a href="{{ url('like', $product->likeOn) }}" style="width: 300px; margin: auto;" class="btn btn-warning mb-2">お気に入りに登録</a>
                    @endif;
                </div>
            </div>
        </div>
        @endforeach


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
        </script>
    </body>

    </html>


</x-app-layout>
