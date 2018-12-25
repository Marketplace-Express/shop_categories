<?php
/*
 * Modified: prepend directory path of current file, because of this file own different ENV under between Apache and command line.
 * NOTE: please remove this comment.
 */
defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

return new \Phalcon\Config([
    'version' => '1.0',
    'api' => [
        'version' => '1.0'
    ],

    'debug' => true,

    'database' => [
        'adapter'  => 'Mysql',
        'host'     => 'localhost',
        'username' => 'phalcon',
        'password' => 'secret',
        'dbname'   => 'shop_categories',
        'charset'  => 'utf8'
    ],

    'cache' => [
        'host' => 'localhost',
        'port' => 6379,
        'auth' => '',
        'persistent' => true,
        'database' => 0
    ],

    'application' => [
        'appDir'         => APP_PATH . '/',
        'modelsDir'      => APP_PATH . '/common/models/',
        'migrationsDir'  => APP_PATH . '/migrations/',
        'cacheDir'       => BASE_PATH . '/cache/',
        'logsDir'        => APP_PATH . '/logs/',

        // This allows the baseUri to be understand project paths that are not in the root directory
        // of the webpspace.  This will break if the public/index.php entry point is moved or
        // possibly if the web server rewrite rules are changed. This can also be set to a static path.
        'baseUri'        => preg_replace('/public([\/\\\\])index.php$/', '', $_SERVER["PHP_SELF"]),

        // Category name validation config
        'minCategoryNameLength' => 3,
        'maxCategoryNameLength' => 100,
        'allowWhiteSpace' => true,
        'allowUnderscore' => true,

        // Category order validation config
        'minCategoryOrder' => 0,
        'maxCategoryOrder' => 999,
        'allowFloat' => false,
        'allowSign' => false
    ],

    /**
     * if true, then we print a new line at the end of each CLI execution
     *
     * If we dont print a new line,
     * then the next command prompt will be placed directly on the left of the output
     * and it is less readable.
     *
     * You can disable this behaviour if the output of your application needs to don't have a new line at end
     */
    'printNewLine' => true
]);
