<?php
/**
 * User: Wajdi Jurry
 * Date: 27/07/19
 * Time: 02:00 ص
 */

namespace app\common\exceptions;


use Throwable;

class OperationFailedException extends \Exception
{
    public function __construct($message = "", $code = 503, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}