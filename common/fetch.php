<?php

function Fetch_Raw($url, $postdata = null, $headers = null) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // make curl_exec result, or false
	if ( isset($headers) ) {
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers );
	}

	if ( isset($postdata) && (is_array($postdata) || is_string($postdata)) ) {
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($curl, CURLOPT_POST, true);
	}
	$response = curl_exec($curl);

	//print_r($url." ".intval($response)."\n");
	//print_r($curl."\n");
	//print_r(curl_error($curl));
	//print_r($response);

	//var_dump( curl_getinfo($curl) );

	curl_close($curl);
	return $response;
}

function Fetch_Json($url, $postdata = null, $headers = null) {
	return json_decode(Fetch_Raw($url, $postdata, $headers), true);
}
