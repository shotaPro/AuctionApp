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
        <h1>請求管理画面</h1>

        <form action="{{ url('bill_search_event') }}" method="GET">
            @csrf
            <h4 class="mt-3">種類</h4>
            <select name="bill_status">
                <option value="">未選択</option>
                <option value="0" {{ $selected_bill_status == 0 ? 'selected' : '' }}>請求未確定</option>
                <option value="1" {{ $selected_bill_status == 1 ? 'selected' : '' }}>請求確定済み</option>
            </select>
            <br>
            <button class="mt-3" type="submit">検索</button>
        </form>
    </div>


    <div style="margin-top: 30px;" class="text-center">
        <h1>請求一覧</h1>
        @if (session()->has('success_message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert">
                    x
                </button>
                {{ session()->get('success_message') }}
            </div>
        @endif


        @if (!empty($not_yet_bill_event_products))


            @if ($not_yet_bill_event_products->isNotEmpty())

                @foreach ($not_yet_bill_event_products->unique('highest_bid_person') as $key => $event)
                    @if ($event->auction_status == 1)
                        @php
                            $key += 1;
                        @endphp

                        <div class="card" style="width: 18rem; margin: auto;">
                            <div class="card-body">
                                <div style="white-space: nowrap">
                                    <h5 class="card-title">No{{ $key++ }}: {{ $event->event_name }}</h5>
                                    <p>落札者ID: {{ $event->highest_bid_person }}</p>
                                    <a style="border-color: black;"
                                        href="{{ url('create_bill_invoice', $event->highest_bid_person) }}">代理送信</a>
                                </div>
                            </div>
                        </div>
                    @elseif($event->auction_status == 3)
                        <div class="card" style="width: 18rem; margin: auto;">
                            <div class="card-body">
                                <div style="white-space: nowrap">
                                    <h5 class="card-title">No{{ $key++ }}: {{ $event->event_name }}</h5>
                                    <p>落札者ID: {{ $event->highest_bid_person }}</p>
                                    <a href="{{ url('create_invoice_pdf', $event->auction_id) }}">請求明細書</a>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @else
                <p style="color: red">検索結果に該当する催事が見つかりませんでした。</p>

            @endif

        @endif

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>
