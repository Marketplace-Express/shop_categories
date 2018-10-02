<?php
/**
 * User: Wajdi Jurry
 * Date: 29/07/18
 * Time: 10:41 م
 */

namespace Shop_categories\Controllers;

use Phalcon\Logger\Adapter\File;
use Phalcon\Mvc\Controller;

class ExceptionhandlerController extends Controller
{
    /**
     * Logging file name
     */
    const LOG_FILE = 'app.log';

    /**
     * @param $errors
     * @param int $code
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function raiseErrorAction($errors, $code)
    {
        // Log error
        $this->logError($errors);

        // response->setStatusCode slows down the performance
        // replacing it with http_response_code
        http_response_code($code);
        return $this->response
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
