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
                        @foreach($shuppin_user as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select><br>
                    <button style="margin-top: 20px;" class="btn btn-primary"type="submit" name="search">検索する</button>
                </form>


                <h2>出品商品一覧</h2>



        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
        </script>
    </body>

    </html>

</x-app-layout>
