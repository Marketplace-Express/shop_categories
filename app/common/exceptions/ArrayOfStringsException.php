<?php
/**
 * User: Wajdi Jurry
 * Date: 19/10/18
 * Time: 04:49 م
 */

namespace Shop_categories\Exceptions;

class ArrayOfStringsException extends \Exception
{
    public function __construct(array $message = [], int $code = 0)
    {
        $this->message = json_encode($message);
        $this->code = $code;
    }
}