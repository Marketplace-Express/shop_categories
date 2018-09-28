<?php
/**

 * User: Wajdi Jurry
 * Date: 29/07/18
 * Time: 10:41 م
 */

namespace Shop_categories\Modules\Api\Controllers;

use Phalcon\Logger\Adapter\File;

class ExceptionhandlerController extends ControllerBase
{
    /**
     * Logging file name
     */
    const LOG_FILE = 'app.log';

    /**
     * @param $errors
     * @param int $code
     */
    public function raiseErrorAction($errors, $code)
    {
        // Log error
        $this->logError($errors);

        $this->response
            ->setStatusCode($code)
            ->setJsonContent([
                'status' => $code,
                'message' => $errors
            ]);
    }

    public function logError($errors)
    {
        $errors = (is_array($errors)) ? implode('\n', $errors) : $errors;
        (new File($this->getDI()->get('config')->application->logsDir . self::LOG_FILE))
            ->log(\Phalcon\Logger::ERROR, $errors);
    }
}
