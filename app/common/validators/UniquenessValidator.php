<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/11/10
 * Time: 16:09
 */

namespace app\common\validators;


use Phalcon\Mvc\Model;
use Phalcon\Validation\Message;
use Phalcon\Validation\Validator;
use Phalcon\Validation\ValidatorInterface;

class UniquenessValidator extends Validator implements ValidatorInterface
{

    public function validate(\Phalcon\Validation $validation, $attribute)
    {
        $message = $this->getOption('message', "{$attribute} should be unique");
        /** @var Model $model */
        $model = $this->getOption('model');

        if (empty($model)) {
            throw new \InvalidArgumentException('UniquenessValidator requires "model" option');
        }

        /** @var \Closure $conditions */
        $conditions = $this->getOption('conditions');

        /** @var Model\Criteria $conditions */
        $conditions = $conditions($model::query());

        if (!$conditions instanceof \Closure) {
            throw new \InvalidArgumentException('conditions should be closure');
        }

        $value = trim($model->{$attribute});
        $conditions->andWhere(sprintf('%s = :value:', $attribute), ['value' => $value]);

        $queryParams = $conditions->getParams();
        $queryParams = array_merge([$queryParams['conditions']], ['bind' => $queryParams['bind']]);

        if ($model::count($queryParams) > 0) {
            $validation->appendMessage(new Message($message, $attribute));
            return false;
        }

        return true;
    }
}