<?php
// Datastore, i.e. Redis data storage engine
// NOTE: This is implemented passively. By design it can be included and the redis connection
//       will only open when an actual redis/datastore request is made.

// Get the datastore instance
function ds() {
	static $redis = null;

	if (is_null($redis)) {
		$redis = new Redis();
		if (defined('REDIS_SOCK') && strlen(REDIS_SOCK)) {
			$redis->connect(REDIS_SOCK);
		}
		else {
			$redis->connect(REDIS_HOST, REDIS_PORT);
		}
		#$redis->setOption(Redis::OPT_PREFIX, REDIS_PREFIX);					// Optionally prefix all keys (might break interoperability)
		#$redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);		// Use built-in serialize/unserialize
		#$redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_IGBINARY);	// Use igBinary serialize/unserialize
		#$redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_MSGPACK);	// Use msgpack serialize/unserialize
	}

	return $redis;
}

function ds_Set($key, $value, $ttl = null) {
	return ds()->set($key, serialize($value), $ttl);
}

function ds_Get($key) {
	$ret = ds()->get($key);
	return $ret ? unserialize($ret) : null;
}

function ds_Unlink($key) {
	return ds()->unlink($key);
}
