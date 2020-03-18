<?php
/**
 * User: Wajdi Jurry
 * Date: 27/02/19
 * Time: 09:24 م
 */

namespace app\common\requestHandler\category;


use app\common\requestHandler\RequestAbstract;
use Phalcon\Validation;
use Phalcon\Validation\Message\Group;

class SearchRequestHandler extends RequestAbstract
{

    /** @var string */
    protected $keyword;

    protected $errorMessages;

    /**
     * @param string $keyword
     */
    public function setKeyword(string $keyword): void
    {
        $this->keyword = $keyword;
    }

    /** Validate request fields using \Phalcon\Validation\Validator
     * @return Group
     */
    public function validate(): Group
    {
        $validator = new Validation();

        $validator->add(
            'keyword',
            new Validation\Validator\PresenceOf()
        );

        return $validator->validate([
            'keyword' => $this->keyword
        ]);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'keyword' => $this->keyword . '*'
        ];
    }
}
