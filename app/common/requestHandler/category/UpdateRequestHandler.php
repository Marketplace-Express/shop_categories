<?php
/**

 * User: Wajdi Jurry
 * Date: 28/07/18
 * Time: 01:47 م
 */

namespace app\common\requestHandler\category;

use app\common\validators\rules\CategoryRules;
use Phalcon\Exception;
use Phalcon\Utils\Slug;
use Phalcon\Validation;
use app\common\controllers\BaseController;
use app\common\exceptions\ArrayOfStringsException;
use app\common\requestHandler\IRequestHandler;
use app\common\utils\UuidUtil;

class UpdateRequestHandler extends BaseController implements IRequestHandler
{
    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string|null $parentId
     */
    private $parentId;

    /** @var int $order */
    private $order;


    public $validator;
    public $errorMessages = [];
    public $uuidUtil;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param null|string $parentId
     */
    public function setParentId(?string $parentId)
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
            'name'      => $this->getName(),
            'parentId'  => $this->getParentId(),
            'order'     => $this->getOrder()
        ];

        return $validator->validate($fields);
    }

    /**
     * @return bool
     */
    public function isValid() : bool
    {
        $messages = $this->validate();

        if (count($messages)) {
            foreach ($messages as $message) {
                $this->errorMessages[$message->getField()] = $message->getMessage();
            }

            return false;
        }

        return true;
    }

    /**
     * @param string $message
     * @throws \Exception
     */
    public function notFound($message = 'Not Found!')
    {
        throw new \Exception($message, 404);
    }

    /**
     * @param null $message
     * @throws ArrayOfStringsException
     */
    public function invalidRequest($message = null)
    {
        if (is_null($message)) {
            $message = $this->errorMessages;
        }
        throw new ArrayOfStringsException($message, 400);
    }

    /**
     * @param null $message
     */
    public function successRequest($message = null)
    {
        $this->response
            ->setJsonContent([
                'status' => 200,
                'message' => $message
            ]);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function toArray(): array
    {
        $result = [];

        if (!empty($this->getName())) {
            $result['categoryName'] = $this->getName();
            $result['categoryUrl'] = (new Slug())->generate($this->name);

        }

        if (!empty($this->getParentId())) {
            $result['categoryParentId'] = $this->getParentId();
        }

        if (!empty($this->getOrder())) {
            $result['categoryOrder'] = $this->getOrder();
        }

        return $result;
    }
}
