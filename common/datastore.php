<?php
// Datastore, i.e. Redis data storage engine

$_redis = new Redis();
$_redis->Connect(REDIS_HOST, REDIS_PORT);
#$_redis->setOption(Redis::OPT_PREFIX, REDIS_PREFIX);	// Optionally prefix all keys (might break interoperability)

function ds_Set($key, $value, $ttl = null) {
	global $_redis;
	return $_redis->set($key, $value, $ttl);
}

function ds_Get($key) {
	global $_redis;
	return $_redis->get($key);
}
