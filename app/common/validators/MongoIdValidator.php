<?php
/**
 * User: Wajdi Jurry
 * Date: ١٦‏/٨‏/٢٠١٩
 * Time: ١:٤٢ م
 */

namespace app\common\validators;

use Phalcon\Validation\Message;
use Phalcon\Validation\Validator;

class MongoIdValidator extends Validator
{

    /**
     * Executes the validation
     *
     * @param \Phalcon\Validation $validation
     * @param string $attribute
     * @return bool
     */
    public function validate(\Phalcon\Validation $validation, $attribute)
    {
        $value = $validation->getValue($attribute);
        $allowEmpty = $this->getOption('allowEmpty', false);
        $message = $this->getOption('message', 'Invalid MongoId');

        if (empty($value) && $allowEmpty) {
            return true;
        }

        if (preg_match('/^[a-f\d]{24}$/i', $value)) {
            return true;
        }

        $validation->appendMessage(new Message($message ?? 'Invalid MongoId', $attribute));
        return false;
    }
}
