<?php
/**
 * User: Wajdi Jurry
 * Date: 27/07/19
 * Time: 01:18 ص
 */

namespace app\common\exceptions;

class NotFoundException extends \Exception
{
    public function __construct($message = "", $code = 404, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}