<?php
/**
 * User: Wajdi Jurry
 * Date: 22/02/19
 * Time: 04:40 م
 */

namespace app\modules\cli\request;

use Phalcon\Di\Injectable;

class Handler extends Injectable
{
    /**
     * @param string $service
     * @param string $method
     * @param $data
     * @return mixed
     *
     * @throws \Exception
     */
    static public function process(string $service, string $method, $data)
    {
        $service = \Phalcon\Di::getDefault()->getAppServices($service);
        if (!is_callable([$service, $method])) {
            throw new \Exception('Method "' . get_class($service) . '::' . $method . '" is not a callable method');
        }
        return call_user_func_array([$service, $method], $data);
    }
}
