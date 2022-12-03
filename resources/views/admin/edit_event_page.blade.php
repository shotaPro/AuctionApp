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
        <a href="{{ url('/event_setting_page') }}">催事一覧に戻る</a>

        <div style="margin-top: 30px;" class="text-center">
            <h2>催事編集</h2>
            @if (session()->has('message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert">
                    x
                </button>
                {{ session()->get('message') }}
            </div>
        @endif
            <div class="card" style="width: 40rem; margin: auto;">
                <div class="card-body">
                    <form action="{{ url('event_edit', $edit_event_page->id) }}" method="POST">
                        @csrf
                        催事名<br>
                        <input name="event_name" type="text" value="{{ $edit_event_page->event_name }}"><br>
                        開始日<br>
                        <input name="start_date" type="date" value="{{ $edit_event_page->start_date}}"><br>
                        終了日<br>
                        <input name="end_date" type="date" value="{{ $edit_event_page->end_date }}"><br>
                        <button style="margin-top: 20px;" type="submit" name="submit"class="btn btn-primary">保存する</button>
                    </form>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
        </script>
    </body>

    </html>

</x-app-layout>
