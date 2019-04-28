<?php

function QueryString_Add(&$qs, $key, $value) {
	if (strlen($qs) > 0) {
		$qs .= "&";
	}
	$qs .= $key . "=" . $value;
}

// Convert a querystring to a flat array of key/value pairs
function QueryString_ToArray($qs) {
	$kv = explode('&', $qs);
	$pairs = [];
	foreach ($kv as &$value) {
		$pairs[] = explode('=', $value);
	}

	return $pairs;
}

// Convert a querystring to an indexable array object, and any time multiple entries come up create an array
function QueryString_Parse($qs) {
	$kv = explode('&', $qs);
	$out = [];
	foreach ($kv as &$value) {
		list($key, $value) = explode('=', $value);
		if (array_key_exists($key, $out)) {
			if (is_array($out[$key])) {
				$out[$key][] = $value;
			}
			else {
				$out[$key] = [$out[$key]];
				$out[$key][] = $value;
			}
		}
		else {
			$out[$key] = $value;
		}
	}

	return $out;
}
