<?php
namespace twitch;

require_once __DIR__."/../config.php";
require_once __DIR__."/querystring.php";
require_once __DIR__."/fetch.php";

const API_BASE = "https://api.twitch.tv/helix/";

function Fetch_Twitch($url, $postdata = null, $_headers = null) {
	$headers = [
	//	"Accept: application/vnd.twitchtv.v5+json",
		"Client-ID: ".TWITCH_CLIENT_ID
	];
	//$headers[] = "Authorization: Bearer ".$token;

	if (!is_null($_headers)) {
		$headers = array_merge($headers, $_headers);
	}

	return Fetch_Json(API_URL.$url, $postdata, $headers);
}
