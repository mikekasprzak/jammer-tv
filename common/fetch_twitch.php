<?php
namespace twitch;

require_once __DIR__."/../config.php";
require_once __DIR__."/querystring.php";
require_once __DIR__."/fetch.php";
require_once __DIR__."/datastore.php";

const API_BASE = "https://api.twitch.tv/helix/";
const ID_BASE = "https://id.twitch.tv/oauth2/";

// Lookup the bearer, if we have it
const BEARER_API_BASE = "TWITCH_BEARER_API_";
$bearer = ds_Get(BEARER_API_BASE."KEY");
$bearer_modified = ds_Get(BEARER_API_BASE."MODIFIED");
// todo: that false check thing

echo "b: ".json_encode($bearer)."    bm: ".$bearer_modified."\n";

function fetch_TwitchID($url, $postdata = null, $headers = []) {
	echo $url."/n";

	return Fetch_Json(ID_BASE.$url, $postdata, $headers);
}

function token_GetClientCredentials() {
	$qs = "";
	QueryString_Add($qs, "client_id", TWITCH_CLIENT_ID);
	QueryString_Add($qs, "client_secret", TWITCH_CLIENT_SECRET);
	QueryString_Add($qs, "grant_type", "client_credentials");

	return fetch_TwitchID("token?".$qs, "");
}

function token_Validate($token = null) {
	global $bearer;
	if (is_null($token)) {
		$token = $bearer;
	}

	if (!$token) {
		$ret = fetch_TwitchID("validate", "" /* trigger HTTP POST */, ["Authorization: OAuth ".$token]);
		return $ret && array_key_exists('client_id', $ret) && ($ret['client_id'] == TWITCH_CLIENT_ID);
	}
	return null;
}

function token_Revoke($token = null) {
	global $bearer;
	if (is_null($token)) {
		$token = $bearer;
	}

	if ($token) {
		$qs = "";
		QueryString_Add($qs, "client_id", TWITCH_CLIENT_ID);
		QueryString_Add($qs, "token", $token);

		return fetch_TwitchID("revoke?".$qs, "" /* trigger HTTP POST */);
	}
	return null;
}

function token_Do() {
	global $bearer;
	global $bearer_modified;

	if (!$bearer) {
		$bearer = token_GetClientCredentials();
		$bearer_modified = time();

		echo "b: ".json_encode($bearer)."    bm: ".$bearer_modified."\n";

		ds_Set(BEARER_API_BASE."KEY", $bearer, 10);
		ds_Set(BEARER_API_BASE."MODIFIED", $bearer_modified, 10);
	}
}
token_Do();



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
