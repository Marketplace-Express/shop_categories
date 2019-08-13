<?php
/**

 * User: Wajdi Jurry
 * Date: 28/07/18
 * Time: 01:47 م
 */

namespace app\common\requestHandler\category;

use app\common\requestHandler\RequestAbstract;
use app\common\validators\rules\CategoryRules;
use Phalcon\Exception;
use Phalcon\Utils\Slug;
use Phalcon\Validation;
use app\common\controllers\BaseController;
use app\common\exceptions\ArrayOfStringsException;
use app\common\requestHandler\IRequestHandler;
use app\common\utils\UuidUtil;

class UpdateRequestHandler extends RequestAbstract
{
    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var string|null */
    private $parentId;

    /** @var int|null */
    private $order;

    public $validator;
    public $errorMessages = [];
    public $uuidUtil;

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param null|string $parentId
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return mixed
     */
    private function getUuidUtil()
    {
        if (!$this->uuidUtil) {
            $this->uuidUtil = new UuidUtil();
        }
        return $this->uuidUtil;
    }
    /**
     * @return CategoryRules
     */
    public function getValidationRules(): CategoryRules
    {
        return new CategoryRules();
    }

    /**
     * @return Validation\Message\Group
     */
    public function validate() : Validation\Message\Group
    {
        $validator = new Validation();
        $validator->add(
            'parentId',
            new Validation\Validator\Callback([
                'callback' => function ($data) {
                    if (!empty($data['parentId'])) {
                        return $this->getUuidUtil()->isValid($data['parentId']);
                    }
                    return true;
                },
                'message' => 'Invalid category parent Id'
            ])
        );

        $validator->add(
            'order',
            new Validation\Validator\NumericValidator([
                'min' => $this->getValidationRules()->minOrder,
                'max' => $this->getValidationRules()->maxOrder,
                'message' => 'Category order should be a number',
                'allowEmpty' => true
            ])
        );

        $validator->add(
            'name',
            new Validation\Validator\AlphaNumericValidator([
                'whiteSpace' => $this->getValidationRules()->allowNameWhiteSpace,
                'underscore' => $this->getValidationRules()->allowNameUnderscore,
                'min' => $this->getValidationRules()->minNameLength,
                'max' => $this->getValidationRules()->maxNameLength,
                'message' => 'Invalid category name',
                'messageMinimum' => 'Category name should be at least 3 characters',
                'messageMaximum' => 'Category name should not exceed 100 characters',
                'allowEmpty' => true
            ])
        );

        // Validate English input
        $validator->add(
            'name',
            new Validation\Validator\Callback([
                'callback' => function ($data) {
                    $name = preg_replace('/[\d\s_]/i', '', $data['name']); // clean string
                    if (preg_match('/[a-z]/i', $name) == false) {
                        return false;
                    }
                    return true;
                },
                'message' => 'English language only supported'
            ])
        );

        // Fields to be validated
        $fields = [
            'name'      => $this->name,
            'parentId'  => $this->parentId,
            'order'     => $this->order
        ];

        return $validator->validate($fields);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function toArray(): array
    {
        $result = [
            'categoryId' => $this->id
        ];

        if (!empty($this->name)) {
            $result['categoryName'] = $this->name;
            $result['categoryUrl'] = (new Slug())->generate($this->name);

        }

        if (!empty($this->parentId)) {
            $result['categoryParentId'] = $this->parentId;
        }

        if (!empty($this->order) && $this->order !== null) {
            $result['categoryOrder'] = $this->order;
        }

        return $result;
    }
}
