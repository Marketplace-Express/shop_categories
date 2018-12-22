<?php
/**
 * User: Wajdi Jurry
 * Date: 19/12/18
 * Time: 09:29 م
 */

namespace Shop_categories\Logger;

use Phalcon\Di\Injectable;
use Phalcon\Logger\Adapter\File;

class ApplicationLogger extends Injectable
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

    public function logError($errors)
    {
        $errors = (is_array($errors)) ? implode('\n', $errors) : $errors;
        $this->getFile()->log(\Phalcon\Logger::ERROR, $errors);
    }
}