<?php

/**
 * @node_name memcache
 * Desc:
 * Created by PhpStorm.
 * User: jasong
 *  http://php.net/manual/zh/memcache.connect.php
 */

namespace XsyCrm\Cache;

use XsyCrm\Exceptions\CacheException;
use Yd\YdRedis;
use Exception;

class RedisCacheStore implements CacheStoreInterface {


	private static $cacheInstance;
	private static $config = [];
	private static $instance = [];


	public static function setConf( $conf ) {
		self::$config = $conf;
	}

	public static function getInstance( $key ) {
		if ( ! isset( self::$config[ $key ] ) ) {
			throw new CacheException( "memcached $key conf not exist" );
		}
		if ( ! isset( self::$instance[ $key ] ) ) {
			try {
				YdRedis::setCfgs( self::$config );
				self::$cacheInstance = YdRedis::ins( $key );
			} catch ( Exception $e ) {
				throw new CacheException( $e->getMessage(), $e->getCode() );
			}

			self::$instance[ $key ] = new self();
		}

		return self::$instance[ $key ];
	}

	public function set( $key, $value, $expire ) {
		self::$cacheInstance->setex( $key, $expire, $value );
	}


	public function get( $key ) {
		return self::$cacheInstance->get( $key );
	}

}