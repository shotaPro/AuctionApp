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
        <div style="border: 1px solid black" class="text-center">

            <form method="POST" action="{{ url('register_product') }}" enctype= multipart/form-data>
                @csrf
                <h1>商品登録画面</h1>

                @if ($errors->any())
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

                @if (session()->has('message'))
                    <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert">
                            x
                        </button>
                        {{ session()->get('message') }}
                    </div>
                @endif

                <div class="input_design">
                    <h4>出品業者</h4>
                    <select style="width: 200px;" name="shuppinn_user" id="">
                        <option value="">---</option>
                        @foreach($shuppin_users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input_design">
                    <h4>商品名</h4>
                    <input name="product_name" type="text"><br>
                </div>
                <div class="input_design">
                    <h4>値段</h4>
                    <input name="product_price" type="number">円<br>
                </div>
                <div class="input_design">
                    <h4>最低落札価格</h4>
                    <input name="product_lowest_price" type="number">円<br>
                </div>
                <div class="input_design">
                    <h4>商品画像</h4>
                    <span style="color: red; font-weight: bold">※必須</span><input name="product_image" class="btn btn-primary"type="file">
                </div>
                <button style="margin-top: 20px;" class="btn btn-primary" type="submit" name="submit">登録する</button>
            </form>


        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
        </script>
    </body>

    </html>

</x-app-layout>
