<?php
/**
 * User: Wajdi Jurry
 * Date: 31/12/18
 * Time: 08:12 م
 */

namespace Shop_categories\RequestHandler\Attributes;


use Phalcon\Validation;
use Phalcon\Validation\Message\Group;
use Shop_categories\Controllers\ControllerBase;
use Shop_categories\Exceptions\ArrayOfStringsException;
use Shop_categories\RequestHandler\RequestHandlerInterface;

class CreateValuesRequestHandler extends ControllerBase implements RequestHandlerInterface
{
    /** @var array $values */
    private $values;

    private $errorMessages;

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param array $values
     */
    public function setValues(array $values)
    {
        $this->values = $values;
    }

    private function getValueConfig()
    {
        return $this->getDI()->getConfig()->application->attributeValueValidationConfig;
    }

    /** Validate request fields using \Phalcon\Validation\Validator
     * @return Group
     */
    public function validate(): Group
    {
        $validator = new Validation();
        $validator->add(
            'values',
            new Validation\Validator\Callback([
                'callback' => function($data) {
                    return !empty($data['values']) && is_array($data['values']);
                },
                'message' => 'Please provide valid values'
            ])
        );

        $validator->add(
            'values',
            new Validation\Validator\Callback([
                'callback' => function($data) {
                    if (array_unique($data['values']) !== $data['values']) {
                        return false;
                    }
                    return true;
                },
                'message' => 'You must provide a unique values'
            ])
        );

        return $validator->validate([
            'values' => $this->getValues()
        ]);
    }

    public function isValid(): bool
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

    public function notFound($message = 'Not Found')
    {
        // TODO: Implement notFound() method.
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
     * @return array
     */
    public function toArray(): array
    {
        return [
            'attribute_values' => $this->getValues()
        ];
    }
}