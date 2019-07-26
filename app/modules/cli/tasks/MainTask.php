<?php
namespace Shop_categories\Modules\Cli\Tasks;

class MainTask extends \Phalcon\Cli\Task
{
    public function mainAction()
    {
        echo "Congratulations! You are now flying with Phalcon CLI!";
    }

    /**
     * Prepare CLI arguments
     * @param array $params
     * @return array
     */
    protected function prepareArguments(array $params)
    {
        $args = [];
        array_map(function ($param) use(&$args) {
            $param = explode('=', $param);
            if (strpos($param[1], '|') !== false) {
                $param[1] = explode('|', $param[1]);
            }
            $args[$param[0]] = $param[1];
        }, $params);
        return $args;
    }

}
