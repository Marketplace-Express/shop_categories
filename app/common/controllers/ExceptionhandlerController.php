<?php
/**
 * User: Wajdi Jurry
 * Date: 29/07/18
 * Time: 10:41 م
 */

namespace app\common\controllers;

use Phalcon\Mvc\Controller;
use app\common\logger\ApplicationLogger;

class ExceptionhandlerController extends Controller
{
    /**
     * Defined as protected for unit test
     * Also, it is encapsulated for this class and its children
     *
     * @return ApplicationLogger
     */
    protected function getLogger(): ApplicationLogger
    {
        return new ApplicationLogger();
    }

    /**
     * @param $errors
     * @param int $code
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function raiseErrorAction($errors, $code)
    {
        if ($jsonErrors = json_decode($errors, true)) {
            array_walk_recursive($jsonErrors, function (&$message) {
                $decoded = json_decode($message, true);
                if ($decoded) {
                    $message = $decoded;
                }
            });
            $errors = $jsonErrors;
        }

        /**
         * Log Error
         * @ignore
         */
        $this->getLogger()->logError($errors);

        // response->setStatusCode slows down the performance
        // replacing it with http_response_code
        http_response_code($code);
        return $this->response
            ->setJsonContent([
                'errors' => $errors
            ]);
    }
}
