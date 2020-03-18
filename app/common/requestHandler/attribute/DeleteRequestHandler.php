<?php
/**
 * User: Wajdi Jurry
 * Date: 29/12/18
 * Time: 10:46 م
 */

namespace app\common\requestHandler\attribute;


use app\common\requestHandler\RequestAbstract;
use Phalcon\Validation\Message\Group;

class DeleteRequestHandler extends RequestAbstract
{

    /** Validate request fields using \Phalcon\Validation\Validator
     * @return Group
     */
    public function validate(): Group
    {
        return new Group();
    }

    public function toArray(): array
    {
        return [];
    }
}
