<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }


            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            .notice {
                color: red
            }
        </style>
    </head>
    <body>
        <div class="flex-center full-height">
            <div class="content">
                <div>
                    <p>youtubeとかの動画をみんなでリスト登録して鑑賞しようアプリ</p>
                </div>
                <div>
                    <form method="GET" onsubmit="location.href = 'room/' + $('#room-number').val(); return false;">
                        <input id="room-number" type="text" placeholder="部屋番号"/>
                        <input value="入室" id="submit-button" type="button" />
                    </form>
                </div>
                <div>
                    <p><a href="{{ url('/app.zip') }}">アプリのダウンロード</a></p>

                </div>
                <div><p>v1.7.3: 実行権限が無くてサーバーが建てられない問題を修正</p></div>
                <div><p>v1.7.4: ニコニコプレミアムだとログインに失敗する問題を修正</p></div>
                <div><p>v1.7.5: ニコニコにログインした場合は自分のアカウントで動画もダウンロードするよう変更。</p></div>
                <div><p>v1.7.6: 細かい不具合の修正、「こまったときは」の追加</p></div>
                <div><p>v1.7.7: 初回起動時に部屋が建てられない不具合の修正</p></div>
            </div>
        </div>
    </body>
    <script>
$(()=>{
    $('#submit-button').click(()=>{
        location.href = "room/" + $('#room-number').val();
    });
});
    </script>
</html>
