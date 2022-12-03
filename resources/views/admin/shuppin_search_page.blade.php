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
        <div style="" class="text-center">

            <h1>出品処理画面</h1>

            @if (session()->has('message'))
                <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert">
                        x
                    </button>
                    {{ session()->get('message') }}
                </div>
            @endif

            <h3>商品検索</h3>
            <h3>業者名</h3>
            <form action="{{ url('search_shuppin_person') }}" method="GET" actionclass="d-flex">
                @csrf
                <select name="user_data" id="">
                    <option value="0" selected>出品者を選択してください</option>
                    @foreach ($shuppin_user as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select><br>
                <button style="margin: 20px 0 20px 0;" class="btn btn-primary"type="submit"
                    name="search">検索する</button>
            </form>

            <h2>出品商品一覧</h2>

            <form metho="GET" action="{{ url('select_shuppin_product') }}">
                @csrf

                <div style="margin-bottom: 30px" class="text-right">

                    <h3>催事</h3>
                    <select name="event_id" id="">
                        <option value="0">出品する催事を選択してください</option>
                        @foreach ($event_info as $ev_info)
                            <option value="{{ $ev_info->id }}">{{ $ev_info->event_name }}</option>
                        @endforeach
                    </select>
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">選択</th>
                            <th scope="col">No</th>
                            <th scope="col">商品コード</th>
                            <th scope="col">商品名</th>
                            <th scope="col">価格</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($product_by_seleted_person as $key => $product_info)
                            @php
                                $key += 1;
                            @endphp
                            <tr>
                                <th><input name="select_product[]" type="checkbox" value="{{ $product_info->id }}"></th>
                                <th>{{ $key++ }}</th>
                                <th>{{ $product_info->id }}</th>
                                <td>{{ $product_info->product_name }}</td>
                                <td>¥ {{ $product_info->product_price }} 円</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <button class="btn btn-primary" type="submit" name="submit">出品する</button>

            </form>


        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
        </script>
    </body>

    </html>

</x-app-layout>
