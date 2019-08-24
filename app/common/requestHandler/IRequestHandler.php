<?php
/**

 * User: Wajdi Jurry
 * Date: 28/07/18
 * Time: 08:06 م
 */

namespace app\common\requestHandler;

use Phalcon\Validation\Message\Group;

interface IRequestHandler
{
    /** Validate request fields using \Phalcon\Validation\Validator
     * @return Group
     */
    public function validate() : Group;


    /**
     * @return bool
     */
    public function isValid() : bool;

    /**
     * @param string $message
     * @return mixed
     */
    public function notFound($message = 'Not Found');

    /**
     * @param null $message
     * @return mixed
     */
    public function invalidRequest($message = null);

    /**
     * @param null $message
     * @return mixed
     */
    public function successRequest($message = null);

    /**
     * @return array
     */
    public function toArray(): array;
}
