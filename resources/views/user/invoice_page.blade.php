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
    <a href="{{ url('/redirect') }}">ホーム画面</a>
    <div style="border: 1px solid black" class="text-center">

    </div>

    <div style="display:flex; justify-content: space-around">

        <div style="border: 1px solid black" class="text-center">
            <div style="margin-top: 30px;" class="text-center">
                <h1>落札利用明細書一覧</h1>

                @foreach($rakusatu_infos->unique('auction_id') as $key => $info)

                @php
                $key += 1;
                @endphp

                    <div class="card" style="width: 18rem; margin: auto;">
                        <div class="card-body">
                            <div class="d-flex justify-content-around">
                                <h5 class="card-title">{{ $info->event_name }}</h5>
                                <a class="btn btn-primary" href="{{ url('create_invoice_pdf_for_user', $info->auction_id) }}">PDF</a>
                                <a class="btn btn-light" href="{{ url('create_csv_for_user', $info->auction_id) }}">CSV</a>
                            </div>
                        </div>
                    </div>

                @endforeach

            </div>
        </div>
        <div style="border: 1px solid black" class="text-center">
            <div style="margin-top: 30px;" class="text-center">
                <h1>出品明細書一覧</h1>

                    <div class="card" style="width: 18rem; margin: auto;">
                        <div class="card-body">
                            <div style="white-space: nowrap">
                                <h5 class="card-title"></h5>
                            </div>
                        </div>
                    </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>
