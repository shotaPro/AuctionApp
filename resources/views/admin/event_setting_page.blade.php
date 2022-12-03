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

            <form action="{{ url('event_setting') }}">
                @csrf
                <h1>催事設定画面</h1>

                @if (session()->has('message'))
                    <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert">
                            x
                        </button>
                        {{ session()->get('message') }}
                    </div>
                @endif

                <div class="input_design">
                    <h4>催事名</h4>
                    <input name="name" type="text"><br>
                </div>
                <div class="input_design">
                    <h4>開催日</h4>
                    <input name="start_date" type="date"><br>
                </div>
                <div class="input_design">
                    <h4>終了日</h4>
                    <input name="end_date" type="date"><br>
                </div>
                <button style="margin-top: 20px;" class="btn btn-primary" type="submit" name="submit">登録する</button>
            </form>

        </div>


        <div style="margin-top: 30px;" class="text-center">
            <h1>催事一覧</h1>

            <div class="card" style="width: 18rem; margin: auto;">
                <div class="card-body">
                    @foreach ($event_all as $key => $event)
                        @php
                            $key = 1;
                        @endphp
                        <h5 class="card-title">{{ $key++ }} {{ $event->event_name }}</h5>
                        <a class="btn btn-primary" href="{{ url('edit_event_page', $event->id) }}">編集する</a><a class="btn btn-danger" href="{{ url('edit_delete', $event->id) }}">削除する</a>
                    @endforeach
                </div>
            </div>

        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
        </script>
    </body>

    </html>

</x-app-layout>
