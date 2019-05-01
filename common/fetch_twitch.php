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
$bearer = null;
$bearer_modified = null;

function fetch_TwitchID($url, $postdata = null, $headers = []) {
	return Fetch_Json(ID_BASE.$url, $postdata, $headers);
}

function token_GetClientCredentials() {
	$qs = "";
	qs_Add($qs, "client_id", TWITCH_CLIENT_ID);
	qs_Add($qs, "client_secret", TWITCH_CLIENT_SECRET);
	qs_Add($qs, "grant_type", "client_credentials");

	return fetch_TwitchID("token?".$qs, "");
}

function token_Validate($token = null): ?bool {
	global $bearer;
	if (is_null($token)) {
		$token = $bearer;
	}

	if (is_string($token)) {
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

	if (is_string($token)) {
		$qs = "";
		qs_Add($qs, "client_id", TWITCH_CLIENT_ID);
		qs_Add($qs, "token", $token);

		return fetch_TwitchID("revoke?".$qs, "" /* trigger HTTP POST */);
	}
	return null;
}

function token_Fetch() {
	global $bearer;
	global $bearer_modified;

	// Attempt 1: Read from cache
	if (!$bearer) {
		$bearer = ds_Get(BEARER_API_BASE."KEY");
		$bearer_modified = ds_Get(BEARER_API_BASE."MODIFIED");

		// TODO: If modified time was a while ago, revalidate
	}

	// Attempt 2: Regenerate
	if (!$bearer) {
		$bearer = token_GetClientCredentials();
		$bearer_modified = time();

		echo "b: ".json_encode($bearer)."    bm: ".$bearer_modified."\n";

		ds_Set(BEARER_API_BASE."KEY", $bearer);
		ds_Set(BEARER_API_BASE."MODIFIED", $bearer_modified);
	}
}


function fetch_TwitchAPI($url, $postdata = null, $_headers = null) {
	global $bearer;

	// Build headers list
	$headers = [
		"Client-ID: ".TWITCH_CLIENT_ID
	];

	if (is_string($bearer)) {
		$headers[] = "Authorization: Bearer ".$bearer;
	}

	if (is_array($_headers)) {
		$headers = array_merge($headers, $_headers);
	}

	// Fetch
	$ret = Fetch_Json(API_BASE.$url, $postdata, $headers, false);
	var_dump($ret[0]);
	var_dump($ret[1]);

	return $ret[2];
}
