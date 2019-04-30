<?php
namespace twitch;

require_once __DIR__."/../config.php";
require_once __DIR__."/querystring.php";
require_once __DIR__."/fetch.php";
require_once __DIR__."/datastore.php";

const API_BASE = "https://api.twitch.tv/helix/";
const ID_BASE = "https://id.twitch.tv/oauth2/";

// Lookup the bearer, if we have it
const BEARER_API_KEY = "TWITCH_BEARER_API_KEY";
$bearer = ds_Get(BEARER_API_KEY);

function fetch_TwitchAPI($url, $postdata = null, $_headers = null) {
	global $bearer;

	// Build headers list
	$headers = [
	//	"Accept: application/vnd.twitchtv.v5+json",
		"Client-ID: ".TWITCH_CLIENT_ID
	];

	if ($bearer) {
		$headers[] = "Authorization: Bearer ".$bearer;
	}

	if (!is_null($_headers)) {
		$headers = array_merge($headers, $_headers);
	}

	// Fetch
	return Fetch_Json(API_URL.$url, $postdata, $headers);
}


function fetch_TwitchID($url, $postdata = null, $_headers = null) {
	// Build headers list
	$headers = [];
//	//	"Accept: application/vnd.twitchtv.v5+json",
//		"Client-ID: ".TWITCH_CLIENT_ID
//	];
//	//$headers[] = "Authorization: Bearer ".$token;

	if (!is_null($_headers)) {
		$headers = array_merge($headers, $_headers);
	}

	// Fetch
	return Fetch_Json(ID_URL.$url, $postdata, $headers);
}

// fetch twitch bearer oath

