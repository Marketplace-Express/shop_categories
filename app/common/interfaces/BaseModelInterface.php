<?php
/**
 * User: Wajdi Jurry
 * Date: 21/12/18
 * Time: 05:15 م
 */

namespace Shop_categories\Interfaces;

use Phalcon\Mvc\ModelInterface;

interface BaseModelInterface extends ModelInterface
{
    public function getSource();

    public static function model();
}