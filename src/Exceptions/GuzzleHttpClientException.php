<?php
/**
 * Desc: HttpClientException
 * Created by PhpStorm.
 * User: jasong
 */

namespace XsyCrm\Exceptions;

use \Exception;

class GuzzleHttpClientException extends Exception {
	const MSG_BODY = 'guzzle body must be string';
	const CODE_BODY = - 1000;
}
