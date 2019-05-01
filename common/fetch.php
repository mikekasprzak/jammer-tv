<?php

function httpHeader_parse($headers) {
	$separator = "\r\n";

	$token = strtok($headers, $separator);

	$ret = [
		'response'=>$token,
		'raw'=>[],
		'key'=>[],
	];

	$token = strtok($separator);
	// IMPORTANT: We're doing a check, but it should be a !== false && strlen check,
	//			  buuut the funny case is a blank string check so this works.
	while ($token) {
		$ret['raw'][] = $token;

		$parts = explode(': ', $token, 2);
		$ret['key'][$parts[0]] = $parts[1];

		$token = strtok($separator);
	}

	return $ret;
}

function fetch_Raw($url, $postdata = null, $headers = null, $body_only = true) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // make curl_exec result, or false

	//curl_setopt($curl, CURLOPT_VERBOSE, 1);
	curl_setopt($curl, CURLOPT_HEADER, 1);

	if ( isset($headers) ) {
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers );
	}

	if ( isset($postdata) && (is_array($postdata) || is_string($postdata)) ) {
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($curl, CURLOPT_POST, true);
	}
	$response = curl_exec($curl);

	$response_code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
	$response_header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
	$response_header = substr($response, 0, $response_header_size);
	$response_body = substr($response, $response_header_size);

	//print_r($url." ".intval($response)."\n");
	//print_r($curl."\n");
	//print_r(curl_error($curl));
	//print_r($response);

	//var_dump( curl_getinfo($curl) );

	curl_close($curl);
	return $body_only ? $response_body : [$response_code, httpHeader_parse($response_header), $response_body];
}

function fetch_Json($url, $postdata = null, $headers = null, $body_only = true) {
	$raw = fetch_Raw($url, $postdata, $headers, $body_only);
	return is_array($raw) ? [$raw[0], $raw[1], json_decode($raw[2], true)] : json_decode($raw, true);
}
