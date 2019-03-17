<?php
/**
 * User: Wajdi Jurry
 * Date: 27/02/19
 * Time: 09:24 م
 */

namespace Shop_categories\RequestHandler\Category;


use Phalcon\Validation;
use Phalcon\Validation\Message\Group;

class AutocompleteRequestHandler extends SearchRequestHandler
{

    /** @var null|string */
    private $scope = null;

    /**
     * @param string|null $scope
     */
    public function setScope(?string $scope)
    {
        $this->scope = $scope;
    }

    public function validate(): Group
    {
        $validator = new Validation();

        $validator->add(
            'scope',
            new Validation\Validator\PresenceOf()
        );

        return $validator->validate([
            'scope' => $this->scope
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
        return parent::isValid() && !count($messages);
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'scope' => $this->scope
        ]);
    }
}