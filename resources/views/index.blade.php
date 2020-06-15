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
                <div><p>v1.4.5 : (web)動画の再生時間を表示。(app)動画の自動スキップにStack数で制限できるように変更。</p></div>
                <div><p>v1.4.6 : (web)リストのデザイン変更。(app)youtube-dlの更新など。</p></div>
                <div><p>v1.4.7 : SoundCloudの再生に失敗する問題の修正。</p></div>
                <div><p>v1.5.0 : (web/app)自分の追加した動画を削除できる機能の追加。※再生中の場合はスキップされません※※一度テストしてヨシ！なので何か起こりそうです</p></div>

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
