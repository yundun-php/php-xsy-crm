<?php

namespace XsyCrm\Cache;

interface CacheStoreInterface {

	public function set( $key, $value, $expire );

	public function get( $key );

}