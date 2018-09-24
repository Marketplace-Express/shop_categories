<?php
/**

 * User: Wajdi Jurry
 * Date: 28/07/18
 * Time: 08:06 م
 */

namespace Shop_categories\RequestHandler;

use Phalcon\Validation\Message\Group;

interface RequestValidationInterface
{
    /** Validate request fields using \Phalcon\Validation\Validator
     * @return Group
     */
    public function validate() : Group;

    public function isValid() : bool;

    public function notFound($message = 'Not Found');

    public function invalidRequest($message = null);

    public function successRequest($message = null);

    public function toArray(): array;
}