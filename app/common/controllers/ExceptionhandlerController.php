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

    private $file;

    /**
     * @return File
     */
    public function getFile()
    {
        if (!$this->file) {
            $this->file = new File($this->getDI()->get('config')->application->logsDir . self::LOG_FILE);
        }
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file): void
    {
        $this->file = $file;
    }

    /**
     * @param $errors
     * @param int $code
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function raiseErrorAction($errors, $code)
    {
        if (!is_array($errors) && !is_object($errors) && ($jsonError = json_decode($errors, true)) != null) {
            $errors = $jsonError;
        }
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
        $this->getFile()->log(\Phalcon\Logger::ERROR, $errors);
    }
}
