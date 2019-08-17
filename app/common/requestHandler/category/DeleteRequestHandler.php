<?php
/**

 * User: Wajdi Jurry
 * Date: 17/08/18
 * Time: 05:08 م
 */

namespace app\common\requestHandler\category;

use app\common\requestHandler\RequestAbstract;
use Phalcon\Validation\Message\Group;

class DeleteRequestHandler extends RequestAbstract
{
    /** @var string */
    private $id;

    public function setId($id)
    {
        $this->id = $id;
    }

    /** Validate request fields using \Phalcon\Validation\Validator
     * @return Group
     */
    public function validate(): Group
    {
        return new Group();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'categoryId' => $this->id
        ];
    }
}
