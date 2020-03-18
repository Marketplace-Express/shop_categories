<?php
/**
 * User: Wajdi Jurry
 * Date: 13/12/18
 * Time: 12:31 ص
 */

namespace app\common\events\listeners;


use Phalcon\Events\Event;

class DbListener
{
    public $query;
    public $conditions;

    public function beforeQuery(Event $event, $connection)
    {

    }
}