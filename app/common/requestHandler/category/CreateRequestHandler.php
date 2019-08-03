<?php
/**
 * User: Wajdi Jurry
 * Date: 28/07/18
 * Time: 01:47 م
 */

namespace app\common\requestHandler\category;

use Exception;
use Phalcon\Config;
use Phalcon\Utils\Slug;
use Phalcon\Validation;
use app\common\controllers\BaseController;
use app\common\exceptions\ArrayOfStringsException;
use app\common\requestHandler\RequestHandlerInterface;
use app\common\utils\UuidUtil;

class CreateRequestHandler extends BaseController implements RequestHandlerInterface
{
    /** @var string $categoryId */
    public $categoryId;

    /** @var string $name */
    private $name;

    /** @var int $order */
    private $order = 0;

    /** @var string $parentId */
    private $parentId;

    public $uuidUtil;
    public $validator;
    public $errorMessages = [];

    /**
     * @return string
     * @throws Exception
     */
    public function getCategoryId()
    {
        return $this->getUuidUtil()->uuid();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @return int|null
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $parentId
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
     * @return UuidUtil
     */
    private function getUuidUtil(): UuidUtil
    {
        if (!$this->uuidUtil) {
            $this->uuidUtil = new UuidUtil();
        }
        return $this->uuidUtil;
    }

    private function getValidationConfig(): Config
    {
        return $this->getDI()->getConfig()->application->validation;
    }

    /**
     * @return Validation\Message\Group
     */
    public function validate() : Validation\Message\Group
    {
        $validator = new Validation();

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

        $validator->add(
            'name',
            new Validation\Validator\AlphaNumericValidator([
                'whiteSpace' => $this->getValidationConfig()->categoryNameValidationConfig->allowWhiteSpace,
                'underscore' => $this->getValidationConfig()->categoryNameValidationConfig->allowUnderscore,
                'min' => $this->getValidationConfig()->categoryNameValidationConfig->minNameLength,
                'max' => $this->getValidationConfig()->categoryNameValidationConfig->maxNameLength,
                'message' => 'Invalid category name',
                'messageMinimum' => 'Category name should be at least 3 characters',
                'messageMaximum' => 'Category name should not exceed 100 characters',
                'allowEmpty' => false
            ])
        );

        $validator->add(
            'parentId',
            new Validation\Validator\Callback([
                'callback' => function ($data) {
                    if (!empty($data['parentId'])) {
                        return $this->getUuidUtil()->isValid($data['parentId']);
                    }
                    return true;
                },
                'message' => 'Invalid parent category Id'
            ])
        );

        $validator->add(
            'order',
            new Validation\Validator\NumericValidator([
                'min' => $this->getValidationConfig()->categoryOrderValidationConfig->minCategoryOrder,
                'max' => $this->getValidationConfig()->categoryOrderValidationConfig->maxCategoryOrder,
                'message' => 'Category order should be a number',
                'allowEmpty' => true
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
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function successRequest($message = null)
    {
        http_response_code(200);
        return $this->response
            ->setJsonContent([
                'status' => 200,
                'message' => $message
            ]);
    }

    /**
     * @param string $message
     * @throws Exception
     */
    public function notFound($message = 'Not Found')
    {
        throw new Exception($message, 404);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function toArray(): array
    {
        $result = [
            'categoryId' => $this->getCategoryId(),
            'categoryParentId' => $this->getParentId(),
            'categoryName' => $this->getName(),
            'categoryOrder' => $this->getOrder(),
            'categoryVendorId' => $this->request->getQuery('vendorId'),
            'categoryUserId' => 'fded67e4-9fcd-4a2d-ae2e-de15d70a8bb5',
            'categoryUrl' => (new Slug())->generate($this->name)
        ];

        return $result;
    }
}
