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

        <style>
            .error {
                color: red;
            }
        </style>
    </head>

    <body>

        <div class="container-fluid">
            <div class="row flex-nowrap">

                <div class="col py-3">
                    <h2 class="text-center mb-3">価格交渉画面</h2>



                    <div class="card" style="margin: auto;">
                        @foreach ($negotiation_products as $key => $product_data)
                            @php
                                $key += 1;
                            @endphp

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">商品名</th>
                                        <th scope="col">商品番号</th>
                                        <th scope="col">入札額</th>
                                        <th scope="col">提示された金額</th>
                                        <th scope="col">受け入れ・拒否</th>
                                        <th scope="col">提示金額</th>
                                        <th scope="col">交渉状況</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @if($product_data->negotiation_status == 1)
                                        <th scope="row">{{ $key++ }}</th>
                                        <th>----</th>
                                        <th>----</th>
                                        <th>----</th>
                                        <th>----</th>
                                        <th>----</th>
                                        <th>----</th>
                                        <td style="background-color: red; color: white;" class="btn">成立</td>
                                        @else

                                        <th scope="row">{{ $key++ }}</th>
                                        <td>{{ $product_data->product_name }}</td>
                                        <td>{{ $product_data->id }}</td>
                                        <td>{{ $product_data->highest_bid }}</td>
                                        <td>--</td>
                                        <td><a class="btn btn-primary" href="">受け入れる</a><a class="btn btn-danger"
                                                href="">拒否</a></td>
                                        <td>
                                            <form id="form" action="{{ url('admin_suggest_price') }}"
                                                method="GET">
                                                @csrf
                                                <input id="price" name="price" type="number">
                                                <button id="btn" class="mt-3 btn btn-success"
                                                    data-id="{{ $product_data->id }}" type="button">提示する</button>
                                            </form>
                                        </td>
                                        @if($product_data->negotiation_status == 2)
                                        <td style="background-color: green; color: white;" class="btn">交渉中</td>
                                        @else
                                        <td>----</td>
                                        @endif
                                        @endif
                                    </tr>
                                </tbody>
                            </table>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
        </script>
        <script>
            let test = null;
            let form = document.querySelectorAll('#form');
            let p = null;

            let suggets_btn = document.querySelectorAll('#btn');

            for (let i = 0; i < suggets_btn.length; i++) {

                suggets_btn[i].addEventListener('click', function(e) {

                    e.preventDefault();


                    let test = e.target.previousElementSibling;
                    let price_value = test;
                    let bid_price = price_value.value;
                    let product_id = e.target.closest('td').previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.textContent;


                    if(price_value.classList.contains('error')){

                        let test2 = price_value.previousElementSibling;
                        price_value = test2

                    }


                    if (price_value.value == "" || price_value.value == undefined) {

                        let check_already_error = document.querySelector('.error');

                        if (check_already_error != null) {

                            if (check_already_error != 0) {

                                check_already_error.remove();

                            }

                        }

                        let p = document.createElement('p');
                        p.textContent = "金額を入力してください。"
                        p.classList.add('error');
                        suggets_btn[i].before(p);


                    } else {

                        let bid_unit = 500;

                        let result = check_bid_unit(price_value.value, bid_unit);

                        let nyuusatu_price = e.target.closest('td').previousElementSibling.previousElementSibling.previousElementSibling.textContent;

                        if (result != true) {

                            let check_already_error = document.querySelector('.error');

                            if (check_already_error != null) {

                                if (check_already_error != 0) {

                                    check_already_error.remove();

                                }

                            }

                            let p1 = document.createElement('p');
                            p1.textContent = "500円単位で入札してください"
                            p1.classList.add('error');
                            suggets_btn[i].before(p1);



                        }else if(Number(bid_price) < Number(nyuusatu_price)) {

                            let p2 = document.createElement('p');
                            p2.textContent = "現在価格より上の金額を入力してください"
                            p2.classList.add('error');
                            suggets_btn[i].before(p2);

                        }else {

                            window.location.href = 'admin_suggest_price?price=' + price_value.value + '&product_id=' + product_id// 通常の遷移

                        }
                    }

                });
            }



            function check_bid_unit(bid_price, bid_unit) {

                if (Number.isInteger(bid_price / bid_unit)) {

                    return true;

                } else {

                    return false

                }

            }
        </script>
    </body>

    </html>


</x-app-layout>
