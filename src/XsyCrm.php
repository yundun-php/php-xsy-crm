<?php

/**
 *
 * XsyCrm::setConfig(xx);
 * XsyCrm::xx(xx, xx);
 * XsyCrm::xx(xx, xx);
 *
 */

namespace XsyCrm;

use XsyCrm\Cache\CacheStoreInterface;
use XsyCrm\Cache\MemCachedStore;
use XsyCrm\Cache\RedisCacheStore;
use XsyCrm\Http\RawRequest;
use XsyCrm\HttpClients\GuzzleHttpClient;
use XsyCrm\Logger\MonologLogger;

class XsyCrm {

	private static $config;
	private static $cache;
	private static $client;
	public static $token;

	public function __construct() {
	}

	public static function setConfig( $config ) {
		self::$config = $config;
	}

	public static function getConfig() {
		return self::$config;
	}


	public static function setCache( CacheStoreInterface $cache ) {
		self::$cache = $cache;
	}

	public static function getCache() {
		if ( ! self::$cache ) {
			$driver = isset( self::getConfig()['normal']['cache_driver'] ) ? self::getConfig()['normal']['cache_driver'] : 'memcached';
			if ( $driver == 'memcached' ) {
				MemCachedStore::setConf( self::$config );
				self::$cache = MemCachedStore::getInstance( 'memcache_xsy-crm' );
			} else if ( $driver == 'redis' ) {
				RedisCacheStore::setConf( self::$config );
				self::$cache = RedisCacheStore::getInstance( 'redis_xsy_crm' );
			}
		}

		return self::$cache;
	}

	public static function getLogger() {
		$logger = null;
		if ( self::getConfig()['normal']['sdk_log_switch'] ) {
			$logger = MonologLogger::getLoggerInstance( __CLASS__, self::getConfig()['normal']['sdk_log'] );
		}

		return $logger;
	}

	public static function getClient() {
		if ( ! self::$client ) {
			self::$client = new GuzzleHttpClient( null, self::getLogger() );
		}

		return self::$client;
	}


	public static function token() {
		$cacheKey = self::getConfig()['xsy']['token_mem_key'];
		$cacheTtl = self::getConfig()['xsy']['token_ttl'];
		$res      = self::getCache()->get( $cacheKey );
		if ( empty( $res ) ) {
			$res = self::apiCallToken();
			if ( $res ) {
				self::getCache()->set( $cacheKey, $res, $cacheTtl );
			}
		}
		$resArr      = json_decode( $res, 1 );
		self::$token = isset( $resArr['access_token'] ) ? $resArr['token_type'] . ' ' . $resArr['access_token'] : '';

		return self::$token;
	}

	/**
	 *
	 * {
	 * "id": 1149802147889832,
	 * "access_token": "237112140c76b524a6ee52a6b492b4107ff18ab93d6a9cde598d2597c14bdad1.MTE0OTc3ODYyOTAwMTYzMQ==",
	 * "issued_at": 1586936473260,
	 * "token_type": "Bearer"
	 * }
	 */
	public static function apiCallToken() {
		$url         = self::getConfig()['xsy']['token_url'];
		$method      = 'POST';
		$body        = self::getConfig()['xsy_token_conf'];
		$body        = RawRequest::build_query( $body );
		$headers     = [
			'Content-Type' => 'application/x-www-form-urlencoded'
		];
		$timeOut     = 20;
		$options     = [];
		$rawResponse = self::getClient()->send( $url, $method, $body, $headers, $timeOut, $options );
		$resBody     = $rawResponse->getBody();

		return $resBody;
	}


	public static function apiCall( $url, $method, $body, $headers = [], $options = [], $version = '2_0', $raw = false, $timeOut = 20 ) {
		$url = self::getConfig()['normal'][ 'base_api_url_rest_v' . $version ] . $url;

		$defaultHeaders = [
			'Authorization' => self::token(),
			'Content-Type'  => 'application/json'
		];
		$headers        = array_merge( $defaultHeaders, $headers );
		if ( strtolower( $method ) == 'get' ) {
			$url .= $body;
		}
		$rawResponse = self::getClient()->send( $url, $method, $body, $headers, $timeOut, $options );
		if ( $raw ) {
			return $rawResponse;
		} else {
			$resBody = $rawResponse->getBody();

			return $resBody;
		}
	}


}