<?php
// Datastore, i.e. Redis data storage engine

$_redis = new Redis();
$_redis->Connect(REDIS_HOST, REDIS_PORT);
#$_redis->setOption(Redis::OPT_PREFIX, REDIS_PREFIX);					// Optionally prefix all keys (might break interoperability)
#$_redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);		// Use built-in serialize/unserialize
#$_redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_IGBINARY);	// Use igBinary serialize/unserialize
#$_redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_MSGPACK);	// Use msgpack serialize/unserialize

function ds_Set($key, $value, $ttl = null) {
	global $_redis;
	return $_redis->set($key, serialize($value), $ttl);
}

function ds_Get($key) {
	global $_redis;
	$ret = $_redis->get($key);
	return $ret ? unserialize($ret) : null;
}

function ds_Unlink($key) {
	global $_redis;
	return $_redis->unlink($key);
}
