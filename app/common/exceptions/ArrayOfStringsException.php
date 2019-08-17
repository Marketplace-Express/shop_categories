<?php
/**
 * User: Wajdi Jurry
 * Date: 19/10/18
 * Time: 04:49 م
 */

namespace app\common\exceptions;

use GraphQL\Error\Error;
use Phalcon\Validation\Message;

class ArrayOfStringsException extends \Exception
{
    /**
     * ArrayOfStringsException constructor.
     * @param string|array|Message\Group $messages
     * @param int $code
     * Code has default value 422, which describes unprocessable entity
     */
    public function __construct($messages, int $code = 422)
    {
        if (is_array($messages) || is_object($messages)) {
            $errors = [];
            foreach ($messages as $key => $message) {
                if ($message instanceof Error) {
                    if ($message->getPrevious() != null) {
                        $errors[$key] = $message->getPrevious()->getMessage();
                    } else {
                        $errors[$key] = $message->getMessage();
                    }
                } elseif ($message instanceof Message) {
                    $errors[$message->getField()] = $message->getMessage();
                } elseif ($message instanceof \Throwable) {
                    $errors[$key] = $message->getMessage();
                } else {
                    $errors[$key] = $message;
                }
            }
        } else {
            $errors = $messages;
        }
        $this->message = json_encode($errors);
        $this->code = $code;
        parent::__construct($this->message, $this->code);
    }
}
