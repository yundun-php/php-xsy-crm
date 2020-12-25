<?php
/**
 * Desc:
 * Created by PhpStorm.
 * User: jasong
 */

namespace XsyCrm\Exceptions;

use \Exception;

class RawRequestException extends Exception {
	const MSG_API_URL = 'must set base api url first';
	const CODE_API_URL = - 1000;
}
