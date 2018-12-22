<?php
/**
 * User: Wajdi Jurry
 * Date: 13/12/18
 * Time: 12:31 ص
 */

namespace Shop_categories\Events\Listeners;


use Phalcon\Events\Event;

class DbListener
{
    public $query;
    public $conditions;

    public function beforeQuery(Event $event, $connection)
    {
        $x = $event;
    }
}