<?php
//Подключения простенького класса для роботы с VK Api
include("VkApi.class.php");

//Начало главной функции
function _pluginMain($arg)
{
    global $site, $pattcontainer, $pattmedia, $pattsearch, $pattinfo, $pattthumb, $radio, $pattradio, $needswitch, $needinfo;
    parse_str($arg);
    $vk               = new VkApi();
    $vk->app_id       = "3795305";
    $vk->access_token = strPar("ACCSES_TOKEN"); 
	
	//Первая страница плагина
    if (@!$url) {
        $ret[] = Container("url=audio", "Мои аудиозаписи", "http://127.0.0.1/umsp/plugins/potter/audio.png");
        $ret[] = Container("url=audio_rec", "Рекомендуемые аудиозаписи", "http://127.0.0.1/umsp/plugins/potter/audio_rec.png");
        $ret[] = Container("url=video", "Мои видеозаписи", "http://127.0.0.1/umsp/plugins/potter/video.png");
        return $ret;
    } 
	
	//Аудиозаписи
    if ($url == "audio") {
        $result = $vk->query("audio.get", array());
        foreach ($result->response as $key => $value)
            $ret[] = Item(str_replace("https://", "http://", $value->url), substr($value->artist . " - " . $value->title, 0, 100) . ".mp3");
        return $ret;
    } 
	
	//Аудиозаписи - рекомендации
    if ($url == "audio_rec") {
        $result = $vk->query("audio.getRecommendations", array());
        foreach ($result->response as $key => $value)
            $ret[] = Item(str_replace("https://", "http://", $value->url), substr($value->artist . " - " . $value->title, 0, 100) . ".mp3");
        return $ret;
    } 
	
	//Видеозаписи -> выбор качества	
	if (substr($url, 0, 10) == "video_view") {
		$result = $vk->query("video.get", array(
			"videos" => substr($url, 14, strlen($url))
		));
		foreach ($result->response[1]->files as $key => $value)
			$ret[] = Item(str_replace("https://", "http://", $value), $key . " - " . $result->response[1]->title);
		return $ret;
	}
	
	//Видеозаписи
    if ($url == "video") {
        $result = $vk->query("video.get", array());
        foreach ($result->response as $key => $value) {
            if ($key != 0 && @!$value->files->external)
                $ret[] = Container("url=video_view/id=" . $value->owner_id . "_" . $value->vid, $value->title, "");
        }
        return $ret;
    }
}
function Container($id, $title, $thumb)
{
    $id = str_replace("&", "&amp;", $id);
    return array(
        'id' => "umsp://plugins/potter?" . "$id",
        'dc:title' => $title,
        'upnp:class' => "object.container",
        'upnp:album_art' => $thumb
    );
}
function Item($url, $title)
{
    $ext = substr($title, strlen($title) - 4);
    switch ($ext):
        case ".jpg":
            $type     = "object.item.imageItem";
            $protocol = "http-get:*:image/jpeg:DLNA.ORG_PN=JPEG_LRG";
            break;
        case "jpeg":
            $type     = "object.item.imageItem";
            $protocol = "http-get:*:image/jpeg:DLNA.ORG_PN=JPEG_LRG";
            break;
        case ".png":
            $type     = "object.item.imageItem";
            $protocol = "http-get:*:image/png:DLNA.ORG_PN=PNG_LRG";
            break;
        case ".mp3":
            $type     = "object.item.audioItem";
            $protocol = "http-get:*:audio/mpeg:*";
            break;
        case ".mkv":
            $type     = "object.item.videoItem";
            $protocol = "http-get:*:video/x-matroska:*";
            break;
        default:
            $type     = "object.item.videoItem";
            $protocol = "http-get:*:*:*";
    endswitch;
    return array(
        "id" => "umsp://plugins/potter?$url",
        "parentID" => "umsp://plugins/potter",
        "dc:title" => $title,
        "upnp:class" => $type,
        "res" => $url,
        "protocolInfo" => $protocol
    );
}
function strPar($par)
{
    static $config;
    if (!$config)
        $config = file_get_contents((function_exists('_getUMSPConfPath') ? _getUMSPConfPath() : '/conf') . '/config');
    preg_match("/POTTER_$par='(.+)'/", $config, $matches);
    return trim($matches[1]);
}
function intPar($par)
{
    return intval(strPar($par));
}
function getPar($param)
{
    $config = file_get_contents((function_exists('_getUMSPConfPath') ? _getUMSPConfPath() : '/conf') . '/config');
    if (preg_match("/$param=\'(.+)\'/", $config, $m)) {
        if (trim($m[1]) != "")
            return $m[1];
    }
    $ret = "";
    return $ret;
}
function modeSwitch()
{
    exec("sudo chmod 666 /tmp/ir_injection && sudo echo G > /tmp/ir_injection && sleep 2 && sudo echo n > /tmp/ir_injection &");
}
?>