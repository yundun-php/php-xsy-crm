<?php
/**
 * Desc: log interface
 * Created by PhpStorm.
 * User: jasong
 */

namespace XsyCrm\Logger;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class MonologLogger {

	private static $logger;


	public static function getLoggerInstance( $name = 'default', $stream_log = '/tmp/log_xsy_crm_demo.log', $level = Logger::DEBUG ) {
		if ( ! isset( self::$logger ) ) {
			// Create the logger
			self::$logger = new Logger( $name );
			// Now add some handlers
			$streamHandler = new StreamHandler( $stream_log, $level );
			$streamHandler->setFormatter( new LineFormatter( "[%datetime%] %channel%.%level_name%: %message% %context% %extra% \n", '', true ) );
			self::$logger->pushHandler( $streamHandler );
		}

		return self::$logger;
	}


}