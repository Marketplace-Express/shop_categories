<?php
/**
 * User: Wajdi Jurry
 * Date: 27/12/18
 * Time: 06:01 م
 */

namespace app\common\requestHandler\attribute;

use app\common\requestHandler\RequestAbstract;
use Phalcon\Validation;
use Phalcon\Validation\Message\Group;

class GetRequestHandler extends RequestAbstract
{
    /** @var string $categoryId */
    private $categoryId;

    /**
     * @return string
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @param string $categoryId
     */
    public function setCategoryId(string $categoryId)
    {
        $this->categoryId = $categoryId;
    }

    /** Validate request fields using \Phalcon\Validation\Validator
     * @return Group
     */
    public function validate(): Group
    {
        $validator = new Validation();
        $validator->add(
            'categoryId',
            new Validation\Validator\Callback([
                'callback' => function($data) {
                    return !empty($data['categoryId']) &&$this->uuidUtil->isValid($data['categoryId']);
                },
                'message' => 'Invalid category id'
            ])
        );

        return $validator->validate(['categoryId' => $this->getCategoryId()]);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [];
    }
}
