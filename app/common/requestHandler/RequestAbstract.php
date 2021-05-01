<?php
/**
 * User: Wajdi Jurry
 * Date: ١٠‏/٨‏/٢٠١٩
 * Time: ٥:٠٣ م
 */

namespace app\common\requestHandler;


use app\common\exceptions\ArrayOfStringsException;
use app\common\exceptions\NotFoundException;
use app\common\interfaces\ApiArrayData;
use app\common\validators\rules\RulesAbstract;
use Phalcon\Di\Injectable;
use Phalcon\Mvc\Controller;

abstract class RequestAbstract extends Injectable implements IRequestHandler
{
    /** @var array */
    public $errorMessages = [];

    /** @var RulesAbstract */
    protected $validationRules;

    /** @var Controller */
    protected $controller;

    /**
     * RequestAbstract constructor.
     * @param RulesAbstract|null $rulesAbstract
     */
    public function __construct(?RulesAbstract $rulesAbstract = null)
    {
        $this->validationRules = $rulesAbstract;
    }

    /**
     * @param null $message
     * @throws ArrayOfStringsException
     */
    final public function invalidRequest($message = null)
    {
        if (is_null($message)) {
            $message = $this->errorMessages;
        }
        throw new ArrayOfStringsException($message, 400);
    }

    /**
     * @param string $message
     * @throws NotFoundException
     */
    final public function notFound($message = 'Not Found')
    {
        throw new NotFoundException($message);
    }

    /**
     * @param null $message
     * @param int $code
     * @return mixed|\Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function successRequest($message = null, int $code = 200)
    {
        http_response_code($code);

        if ($message instanceof ApiArrayData) {
            $message = $message->toApiArray();
        }

        if (is_array($message)) {
            array_walk($message, function (&$data) {
                $data = ($data instanceof ApiArrayData) ? $data->toApiArray() : $data;
            });
        }

        if ($code != 204) {
            $this->response
                ->setJsonContent([
                    'status' => $code,
                    'message' => $message
                ]);
        }
        return $this->response;
    }

    /**
     * @return bool
     */
    final public function isValid(): bool
    {
        $this->errorMessages = $this->validate();
        return !count($this->errorMessages);
    }
}
