<?php
/**
 * User: Wajdi Jurry
 * Date: ١٠‏/٨‏/٢٠١٩
 * Time: ٥:٠٣ م
 */

namespace app\common\requestHandler;


use app\common\exceptions\ArrayOfStringsException;
use app\common\exceptions\NotFoundException;
use app\common\validators\rules\RulesAbstract;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\ControllerInterface;

abstract class RequestAbstract  implements IRequestHandler, ControllerInterface
{
    /** @var array */
    public $errorMessages = [];

    /** @var RulesAbstract */
    protected $validationRules;

    /** @var Controller */
    protected $controller;

    /**
     * RequestAbstract constructor.
     * @param Controller $controller
     * @param RulesAbstract|null $rulesAbstract
     */
    public function __construct(Controller $controller, ?RulesAbstract $rulesAbstract = null)
    {
        $this->controller = $controller;
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
        if ($code != 204) {
            $this->controller->response
                ->setJsonContent($message);
        }
        return $this->controller->response;
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
