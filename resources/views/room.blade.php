<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/locale/ja.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                margin: 0;
                display: flex;
                flex-direction: column;
                height: 100%;
            }



            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
                margin-bottom: 10px;
                margin-top: 10px;
            }


            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
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

            .body-table{
                overflow-y: scroll;
                display:grid;
            }

            .center-text{
                text-align: center;
            }

            .left-text{
                text-align: left;
            }

            .invisible{
                display: none;
            }

            .current-playing{
                background-color: #b3e5fc;
            }

            .deleted{
                background-color: #999999;
            }

            input[type="text"] {
                font-size: 16px;
                transform: scale(calc(12 / 16));
            }

        </style>
    </head>
    <body>
        <div class="header-form">
            <div class="flex-center invisible" id="masterIdView" >
                アプリ入力用キー:<input type="text" id="masterIdText" readonly/> <input type="submit" id="copy-button" value="コピー" />
            </div>
            <div class="flex-center">
                <a href="{{ url('/') }}">トップに戻る</a>
            </div>
            <div class="flex-center">
                <form method="GET" onsubmit="return false;">
                    <div><label>ユーザー名(任意)</label><input id="username" type="text" maxlength="30"/></div>
                    <div><label>youtube/niconico/soundcloud URL</label><input id="url" type="text"/></div>
                    <div><label>カスタムタイトル</label><input id="title" type="text" placeholder="空欄の場合はURLから取得" /></div>
                    <input value="URL送信" id="submit-button" type="submit" />
                </form>
            </div>
        </div>
        <div class="body-table">
            <table class="list-table">
            </table>
        </div>

    </body>
    <script>

$(()=>{
    $('#submit-button').click(()=>{send()});
    setInterval(()=>{getList()}, 30000);
    getList();
    var cookies = document.cookie;
    var cookiesArray = cookies.split(';');

    if($.cookie("name") != null){
        $('#username').val(decodeURIComponent($.cookie("name")));
    }
    $('#copy-button').click((e)=>{
        var copyTarget = document.getElementById("masterIdText");
        copyTarget.select();
        document.execCommand("copy");

    });

    $(document).on("click", ".delete-button", function (event) {

        if(!sending){
        sending = true;
        $('#submit-button').prop("disabled", true);
        $('#delete-button').prop("disabled", true);

        $.post(
                '{{ url('/') }}/api/youtubelist/remove',
                {
                    'room_id': '{{ $room_id }}',
                    'index': $(event.currentTarget).data('id'),
                    'uuid': $.cookie("uuid"),
                },
            ).done(function(data, textStatus, jqXHR){
                getList();
            }).fail(function(jqXHR, textStatus, errorThrown){
                alert(jqXHR.responseJSON.error);
            }).always(function(){
                sending = false;
                $('#submit-button').prop("disabled", false);
                $('#delete-button').prop("disabled", false);
            });

        }
    });

});
let sending = false;
function send(){
    if(!sending){
        sending = true;
        $('#submit-button').prop("disabled", true);
        $('#delete-button').prop("disabled", true);

        $.post(
                '{{ url('/') }}/api/youtubelist/send',
                {
                    'url': $('#url').val(),
                    'room_id': '{{ $room_id }}',
                    'username': $('#username').val(),
                    'title': $('#title').val(),
                    'uuid': $.cookie("uuid"),
                },
            ).done(function(data, textStatus, jqXHR){
                getList();
                $('#url').val('');
                $('#title').val('');
            }).fail(function(jqXHR, textStatus, errorThrown){
                alert(jqXHR.responseJSON.error);
            }).always(function(){
                sending = false;
                $('#submit-button').prop("disabled", false);
                $('#delete-button').prop("disabled", false);
            });

        $.cookie("name", encodeURIComponent($('#username').val()) , { expires: 30 });
    }
}
function getList(){

    $.getJSON(
        '{{ url('/') }}/api/youtubelist/getList',
        {
            'room_id': '{{ $room_id }}',
            'uuid': ($.cookie("uuid") || ""),
        },
        (data)=>{
            var $table = $('.list-table');
            $table.empty();
            $table.append('<tr><th>時刻</th><th>送信者</th><th>再生時間</th><th class="left-text">タイトル</th></tr>');
            data.data.reverse();
            $.each(data.data, (index, val) =>{
                let time = moment(val.time * 1000);
                $table.append('<tr' + (data.info.currentIndex == (data.data.length - index - 1) ? ' class="current-playing"' : val['deleted'] ? ' class="deleted"' : '') + '>'
                + '<td class="center-text">'+ time.format('YYYY年MM月DD日 HH:mm') +'</td>'
                + '<td class="center-text">' + (val.username || '未入力') + (val.removable ? '<input type="button" class="delete-button" value= "削除" data-id="' + (data.data.length - index - 1)  + '" />' : '') + '</td>'
                + '<td class="center-text">' + val.length + '</td>'
                + '<td><a href="'+ val.url +'">' + val.title + '</a></td>'
                + '</tr>');
            });
            if(data.privateInfo != null && data.privateInfo.masterId != null){
                $('#masterIdView').removeClass("invisible");
                $('#masterIdText').val(data.privateInfo.masterId);
            }
            if(data.privateInfo != null && data.privateInfo.uuid != null){
                $.cookie("uuid", data.privateInfo.uuid, { expires: 30 });
            }
        }
    );
}
    </script>
</html>
