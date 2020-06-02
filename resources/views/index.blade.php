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
                    <p><span class="notice">URL変更しました。以降は次のURLを使用してください。</span> <a href="https://lntk.info/youtube-list/">https://lntk.info/youtube-list/</a></p>
                </div>
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
                    <a href="{{ url('/app.zip') }}">アプリのダウンロード</a>
                </div>
                <div><p>v1.3.0 : アプリキーを生成するようになりました。（一番最初に部屋に入った人のみ表示）<br>アプリキーをアプリの方に入力することで現在再生中のものが表示されるようになります。<br>一部の記号が入っているとファイルが取得できない問題を修正。<br>アプリに表示されるURLを短縮URLに変更。</p></div>
                <div><p>v1.4.0 : 動画を指定秒数経過で自動スキップする機能を追加。</p></div>
                <div><p>v1.4.5 : (web)動画の再生時間を表示。(app)動画の自動スキップにStack数で制限できるように変更。</p></div>
                <div><p>v1.4.6 : (web)リストのデザイン変更。(app)youtube-dlの更新など。</p></div>
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
