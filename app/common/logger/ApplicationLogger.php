<?php
/**
 * User: Wajdi Jurry
 * Date: 19/12/18
 * Time: 09:29 م
 */

namespace app\common\logger;

use Phalcon\Di\Injectable;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\File;

class ApplicationLogger extends Injectable
{
    /**
     * Logging file name
     */
    const LOG_FILE = 'app.log';

    /**
     * @return File
     */
    public function getFile()
    {
        $config = $this->di->getConfig()->application;
        if (!file_exists($config->logsDir . self::LOG_FILE)) {
            touch($config->logsDir . self::LOG_FILE);
        }
        return new File($config->logsDir . self::LOG_FILE);
    }

    public function logError($errors)
    {
        $errors = (is_array($errors)) ? json_encode($errors) : $errors;
        $this->getFile()->log(Logger::ERROR, $errors);
    }
}