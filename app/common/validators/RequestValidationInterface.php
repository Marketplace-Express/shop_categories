<?php
/**
 * Created by PhpStorm.
 * User: wajdi
 * Date: 28/07/18
 * Time: 08:06 م
 */

namespace Shop_categories\Validators;


use Phalcon\Http\Response;
use Phalcon\Validation\Message\Group;

interface RequestValidationInterface
{
    /** Validate request fields using \Phalcon\Validation\Validator
     * @return Group
     */
    public function validate() : Group;

    public function isValid() : bool;

    public function notFound($message = 'Not Found'): Response;

    public function invalidRequest($message = null) : Response;

    public function successRequest($message = null) : Response;
}