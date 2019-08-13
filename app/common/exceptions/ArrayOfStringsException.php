<?php
/**
 * User: Wajdi Jurry
 * Date: 19/10/18
 * Time: 04:49 م
 */

namespace app\common\exceptions;

class ArrayOfStringsException extends \Exception
{
    /**
     * ArrayOfStringsException constructor.
     * @param array $messages
     * @param int $code
     * Code has default value 422, which describes unprocessable entity
     */
    public function __construct(array $messages = [], int $code = 422)
    {
        $errors = [];
        foreach ($messages as $key => $message) {
            if ($message instanceof \Throwable) {
                $errors[$key] = $message->getMessage();
            } else {
                $errors[$key] = $message;
            }
        }
        $this->message = json_encode($errors);
        $this->code = $code;
        parent::__construct($this->message, $this->code);
    }
}
