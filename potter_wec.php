<?php
include ("info.php");
if (defined("WECVERSION") && WECVERSION >= 3)
{
	include_once("/usr/share/umsp/funcs-config.php");
	$descr = $thumb = $art = $name = $version = $date = $author = $desc = $url = $id = "";
	extract($pluginInfo);
	if ($thumb || $art)
		$descr = "<div style='float: left; padding: 4px 10px 4px 4px;'><img src='".($thumb ? $thumb : $art)."' width='60' height='60' alt='logo'></div>";
	$descr  .= "<div>$name v$version ($date) by $author.<br>$desc<br>Information: <a href='$url'>$url</a><br><a href='https://oauth.vk.com/authorize?client_id=3682744&v=5.7&scope=audio,video,offline&redirect_uri=http://oauth.vk.com/blank.html&display=page&response_type=token' target=_bank>Get a token!</a> | <a href='/umsp/plugins/links.php' target=_bank>Edit links!</a></div></div>";
	$key = strtoupper("{$id}_DESC");
	potter($key, $descr, $longdesc, NULL, WECT_DESC);
	potter($id, "Enable $name UMSP plugin","", NULL, WECT_BOOL, array("off", "on"));
	$wec_options[$id]["readhook"]  = wec_umspwrap_read;
	$wec_options[$id]["writehook"] = wec_umspwrap_write;

	potter("ACCSES_TOKEN", "For get token click on the link above! After you agree, copy tapes from the address mentioned parametr 'access_token'","Токен","none", WECT_TEXT);
}

function potter($key, $desc, $longdesc, $def, $typ, $avv=NULL, $avn=NULL)
{
	global $wec_options, $name, $pri;
	if (! is_null($def)) $key = "POTTER_$key";
	$wec_options["$key"] = array(
		"configname"   => "$key",
		"configdesc"   => $desc,
		"longdesc"     => $longdesc,
		"group"        => $name,
		"type"         => $typ,
		"page"         => WECP_UMSP,
		"displaypri"   => $pri++,
		"defaultval"   => $def,
		"availval"     => $avv,
		"availvalname" => $avn
		);
}
?>
