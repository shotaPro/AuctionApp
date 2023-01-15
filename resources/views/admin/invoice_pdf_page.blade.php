@php

    namespace App\Http\Controllers;

    use Carbon\Carbon;

    use Illuminate\Http\Request;
    use App\Models\Auction_event;
    use App\Models\Product;
    use App\Models\User;

    use Illuminate\Support\Facades\Auth;

    foreach ($invoice_data as $data) {
        $rakusatu_user_name = User::Where('id', '=', $data['highest_bid_person'])
            ->select('name')
            ->first();
        $shuppin_user_name = User::Where('id', '=', $data['user_id'])
            ->select('name')
            ->first();
    }

    // dd($invoice_data);

@endphp


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="sheet.css">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="admin/assets/css/event.css">
    <style>
        /* 印刷時の用紙設定 */
        @page {
            size: A4;
            /* 用紙サイズ */
            margin: 0;
            /* ヘッダー・フッダーを無効化 */
        }

        /* 要素の初期化 */
        * {
            /* マージン・パディングをリセットした方がデザインしやすい */
            margin: 0;
            padding: 0;
            /* デフォルトのフォント */
            color: black;
            font-family: "游ゴシック Medium", "Yu Gothic Medium", "游ゴシック体", YuGothic,
                sans-serif;
            font-size: 11pt;
            font-weight: normal;
            /* 背景色・背景画像を印刷する（Chromeのみで有効） */
            -webkit-print-color-adjust: exact;
        }

        /* リスト初期化 */
        ul {
            list-style: none;
            padding-left: 0;
        }

        /* ページレイアウト (section.sheet を１ページとする) */
        .sheet {
            overflow: hidden;
            position: relative;
            box-sizing: border-box;
            page-break-after: always;

            /* 用紙サイズ A4 */
            height: 297mm;
            width: 210mm;

            /* 余白サイズ */
            padding-top: 32mm;
            padding-left: 27mm;
            padding-right: 27mm;
        }

        /* プレビュー用のスタイル */
        @media screen {
            body {
                background: #e0e0e0;
            }

            .sheet {
                background: white;
                /* 背景を白く */
                box-shadow: 0 0.5mm 2mm rgba(0, 0, 0, 0.3);
                /* ドロップシャドウ */
                margin: 5mm auto;
            }
        }
    </style>
</head>

<body>
    <section class="sheet">

        <div class="d-flex justify-content-between mb-3">
            <h2>{{ $rakusatu_user_name['name'] }} 様</h2>
            <h2>株式会社: {{ $shuppin_user_name['name'] }}</h2>
        </div>

        <h1 class="text-center">落札利用明細書</h1>

        <div>
            @foreach ($invoice_data as $key => $data)

                @php

                    $key += 1;

                @endphp

                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">商品名</th>
                            <th scope="col">価格</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">No: {{ $key++ }}</th>
                            <td>{{ $data['product_name'] }}</td>
                            <td>{{ $data['highest_bid'] }}</td>
                        </tr>
                    </tbody>
                </table>
            @endforeach

            <div style="border: 1px solid black; width: 150px;" class="float-end">

                <p>合計金額:{{ number_format($total_amount) }}(税抜)</p>
                <p>合計金額: {{ number_format($total_amount_tax) }}(税込)</p>
            </div>
        </div>


    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>
