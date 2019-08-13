<?php
/**
 * User: Wajdi Jurry
 * Date: ١١‏/٨‏/٢٠١٩
 * Time: ١٢:٠٢ ص
 */

namespace app\common\exceptions;


use Throwable;

class InvalidArgumentException extends \Exception
{
    public function __construct($message = "", $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
