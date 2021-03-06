<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use SimpleXMLElement;
use Illuminate\Support\Str;
use DateTime;
use DatePeriod;
use DateInterval;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\Facades\Twitter;

class YoutubeListController extends Controller
{
    const URL_TYPE_YOUTUBE = 1;
    const URL_TYPE_NICONICO = 2;
    const URL_TYPE_SOUNDCLOUD = 3;
    const URL_TYPE_TWITTER = 4;

    public function send(Request $request){
        if(!$request->has('url')){
            return response(['error' => 'URL未入力'],400);
        }
        if(!$request->has('room_id')){
            return response(['error' => 'room_idが入っていない'],400);
        }
        if(!$request->has('uuid')){
            return response(['error' => 'uuidがない'],400);
        }
        if($request->has('username') && mb_strlen($request->input('username')) > 20){
            return response(['error' => '名前が長すぎる'],400);
        }
        $room_id = $request->input('room_id');
        $url = $request->input('url');
        $uuid = $request->input('uuid');
        $title = $request->input('title', null);
        if(mb_strlen($title) > 50){
            return response(['error' => 'カスタムタイトルが長すぎる'],400);

        }

        if(strpos($url, 'https://youtu.be/' === 0)){
            $url = 'https://www.youtube.com/watch?v=' . substr($url, strlen('https://youtu.be/'));
        }

        //URL判別
        $urlType = 0;
        if(strpos($url, 'https://www.youtube.com/') === 0 || strpos($url, 'https://m.youtube.com/') === 0){
            $urlType = self::URL_TYPE_YOUTUBE;
        }else if(strpos($url, 'https://www.nicovideo.jp/watch/sm') === 0 || strpos($url, 'https://sp.nicovideo.jp/watch/sm') === 0){
            $urlType = self::URL_TYPE_NICONICO;
        }else if(strpos($url, 'https://soundcloud.com/') === 0 ){
            $urlType = self::URL_TYPE_SOUNDCLOUD;
        }else if(strpos($url, 'https://twitter.com/') === 0){
            $urlType = self::URL_TYPE_TWITTER;
        }else{
            return response(['error' => '無効なyoutube/ニコニコ/SoundCloud/twitterのURLです'], 400);
        }

        //URL種類に応じてタイトルと動画の長さを取得
        $jsonUrl = '';
        if($urlType === self::URL_TYPE_YOUTUBE){
            $urlParam = parse_url($url, PHP_URL_QUERY);
            parse_str($urlParam, $params);

            $api = env('YOUTUBE_API');
            $requestUrl = 'https://www.googleapis.com/youtube/v3/videos?id=' . $params['v'] . '&key=' . $api . '&part=snippet';
            $result = json_decode(file_get_contents($requestUrl), true);
            $title = $title ?? $result['items'][0]['snippet']['title'] ?? null;
            $jsonUrl = 'https://www.youtube.com/watch?v=' . $params['v'];
            $requestUrl = 'https://www.googleapis.com/youtube/v3/videos?id=' . $params['v'] . '&key=' . $api . '&part=contentDetails';
            $result = json_decode(file_get_contents($requestUrl), true);
            $length = $result['items'][0]['contentDetails']['duration'];
            $interval = new DateInterval($length);
            $length = $interval->format("%H:%I:%S");
            if(strpos($length, '00:') === 0){
                $length = substr($length, strlen('00:'));
            }
            if(strpos($length, '0') === 0){
                $length = substr($length, strlen('0'));
            }
            if($title === null){
                return response(['error' => '動画タイトルの取得に失敗しました。'], 400);
            }
        }else if($urlType === self::URL_TYPE_NICONICO){
            if(strpos($url, 'https://www.nicovideo.jp/watch/sm') === 0){
                preg_match('/^[0-9]+/', substr($url, strlen('https://www.nicovideo.jp/watch/sm')), $param);
            }else if(strpos($url, 'https://sp.nicovideo.jp/watch/sm') === 0){
                preg_match('/^[0-9]+/', substr($url, strlen('https://sp.nicovideo.jp/watch/sm')), $param);
            }
            $param = $param[0];
            $result = file_get_contents('https://ext.nicovideo.jp/api/getthumbinfo/sm' . $param);
            $xml = new SimpleXMLElement($result);
            $title = $title ?? $xml->thumb->title->__toString();
            $length = $xml->thumb->length->__toString();

            $jsonUrl = 'https://www.nicovideo.jp/watch/sm' . $param;
            if($title === null){
                return response(['error' => '動画タイトルの取得に失敗しました。'], 400);
            }
        }else if ($urlType === self::URL_TYPE_SOUNDCLOUD){

            $path = parse_url($url, PHP_URL_PATH);

            if(!preg_match('/^\/[a-zA-z0-9\-]+\/[a-zA-z0-9\-]+$/', $path)){
                return response(['error' => '動画タイトルの取得に失敗しました。'], 400);
            };
            $ch = curl_init();
            exec('curl ' .escapeshellcmd('https://soundcloud.com' . $path),$result);
            if($title == null){
                $titleRow = null;
                foreach ($result as $row) {
                    // dump(strstr($row, '<title>'));
                    if(strpos($row, '<title>') != false){
                        preg_match('/^[^(\|)]*/',substr($row,strlen('  <title>')),$title);
                        $title = $title[0];
                        // dump($title);
                        if(strpos($title, 'SoundCloud') !== false){
                            return response(['error' => '動画タイトルの取得に失敗しました。'], 400);
                        }


                        rtrim($title, ' ');
                        break;
                    }
                }
            }
            // dump($title);
            $jsonUrl = escapeshellcmd('https://soundcloud.com' . $path);
            // dd();
        }else if($urlType === self::URL_TYPE_TWITTER){
            $param = explode('/',$url);
            $param = $param[count($param) - 1] ?? null;
            preg_match('/[0-9]+/',$param, $result);
            $param = $result[0];
            $result = Twitter::get('statuses/show/' . $param);

            if(($result->extended_entities->media[0]->type ?? null) != 'video'){
                return response(['error' => '動画じゃないっぽいです。広告とかはダメです。'], 400);
            }
            $title = $title ?? $result->text;
            $lengthSecond = $result->extended_entities->media[0]->video_info->duration_millis / 1000;
            $length = floor($lengthSecond / 60) . ':' . sprintf('%02d',$lengthSecond % 60);

            $jsonUrl = 'https://twitter.com/1/status/' . $param;
            if($length === null){
                return response(['error' => '動画タイトルの取得に失敗しました。'], 400);
            }
        }
        //jsonに追加して成功レスポンスを返却
        $json = json_decode(Storage::disk('local')->get($room_id . '.json'),true);
        $time = new Carbon();
        $json['data'][] = [
            'time' => $time->getTimestamp(),
            'url' => $jsonUrl,
            'title' => htmlspecialchars($title),
            'username' => htmlspecialchars($request->input('username')),
            'length' => $length ?? '',
            'deleted' => false,
        ];
        $json['privateInfo']['senderUUIDArray'][count($json['data']) - 1] = $uuid;
        Storage::disk('local')->put($room_id . '.json', json_encode($json));
        return response([]);

    }

    public function getList(Request $request){
        $isMaster = false;
        if(!$request->has('room_id')){
            return response(['error' => 'error'], 400);
        }
        $room_id = $request->get('room_id');
        $uuid = $request->get('uuid');
        $masterId = $request->input('master_id', null);

        //部屋が無かったり、最後の登録が3日前だったりした場合は部屋主になれる
        if(!Storage::disk('local')->exists($room_id . '.json')){
            $isMaster = true;
            $this->createJson($room_id);
        }else {
            $json =  json_decode(Storage::disk('local')->get($room_id . '.json'), true);
            if(count($json['data']) != 0){
                $jsonTime = new Carbon($json['data'][count($json['data']) - 1]['time']);
                $deleteTime = new Carbon();
                $deleteTime->addDays(-3);
                if($deleteTime->gt($jsonTime)){
                    $this->createJson($room_id);
                    $isMaster = true;
                }
            }else{
                $currentTime = new Carbon();
                $createRoomTime = new Carbon($json['privateInfo']['createRoomDate'] ?? 0);
                $currentTime->addDays(-1);
                if($currentTime->gt($createRoomTime)){
                    $this->createJson($room_id);
                    $isMaster = true;
                }
            }

        }
        $json =  json_decode(Storage::disk('local')->get($room_id . '.json'), true);
        //jsonのmasterIdとUUIDが一致する場合は部屋主となる
        if($json['privateInfo']['masterId'] === $masterId){
            $isMaster = true;
        }
        //自分の投稿した動画か、部屋主の場合は動画を削除できるフラグがtrueになる
        if($uuid != null){
            foreach ($json['data'] as $key => $value) {
                $json['data'][$key]['removable'] = $json['info']['currentIndex'] < $key &&  (!($value['deleted'] ?? false)  && ($isMaster || $json['privateInfo']['senderUUIDArray'][$key] === $uuid));
            }
        }
        //見せられないデータを消す
        if(!$isMaster){
            unset($json['privateInfo']);
        }
        unset($json['privateInfo']['senderUUIDArray']);
        if($uuid == null){
            $uuid = Str::uuid();
            $json['privateInfo']['uuid'] = $uuid;
        }
        return $json;
    }

    public function setCurrentIndex(Request $request){
        if(!$request->has('room_id') || !$request->has('master_id') || !$request->has('index')){
            return response(['error' => 'error'], 400);
        }
        $room_id = $request->get('room_id');
        $master_id = $request->get('master_id');
        $index = $request->get('index');

        if(!Storage::disk('local')->exists($room_id . '.json')){
            return response(['error' => 'error'], 400);
        }
        $json =  json_decode(Storage::disk('local')->get($room_id . '.json'), true);
        //アプリから部屋主としてログインしている場合は再生インデックスをセットできる
        if($json['privateInfo']['masterId'] != $master_id || count($json['data']) < $index){
            return response(['error' => 'error'], 400);
        }
        $json['info']['currentIndex'] = $index;
        Storage::disk('local')->put($room_id . '.json', json_encode($json));

        return response([]);
    }


    public function remove(Request $request){
        if(!$request->has('room_id') || !$request->has('index') || !$request->has('uuid')){
            return response(['error' => 'error'], 400);
        }
        $room_id = $request->get('room_id');
        $index = $request->get('index');
        $uuid = $request->get('uuid');
        $masterId = $request->input('master_id', null);
        $isMaster = false;

        if(!Storage::disk('local')->exists($room_id . '.json')){
            return response(['error' => 'error'], 400);
        }

        $json =  json_decode(Storage::disk('local')->get($room_id . '.json'), true);
        if($json['privateInfo']['masterId'] === $masterId){
            $isMaster = true;
        }
        //自分の投稿した動画か、部屋主は動画を削除できる
        if(count($json['data']) < $index || ($json['privateInfo']['senderUUIDArray'][$index] != $uuid && !$isMaster)){
            return response(['error' => 'error'], 400);
        }
        $json['data'][$index]['deleted'] = true;
        Storage::disk('local')->put($room_id . '.json', json_encode($json));

        return response([]);
    }


    public function sendError(Request $request){
        $json['addVersion'] = $request->get('addVersion');
        $json['sendTime'] = $request->get('sendTime');
        $json['condition'] = $request->get('condition');
        $json['stackTrace'] = $request->get('stackTrace');
        $json['roomId'] = $request->get('roomId');
        $json['currentIndex'] = $request->get('currentIndex');
        $json['currentTitle'] = $request->get('currentTitle');
        $date = Carbon::now();
        Storage::disk('local')->put('errorlog/' . $date->format('Ymd_His') . '.json', json_encode($json, JSON_PRETTY_PRINT));

        return response([]);
    }


    private function createJson($roomId){
        $time = new Carbon();
        Storage::disk('local')->put($roomId . '.json','{"data":[],"privateInfo":{"masterId":"'. Str::uuid() .'","createRoomDate": '. $time->getTimestamp() .'},"info":{"currentIndex":-1}}');

    }
}
