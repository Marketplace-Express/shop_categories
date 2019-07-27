<?php
/**
 * User: Wajdi Jurry
 * Date: 02/03/19
 * Time: 05:29 م
 */

namespace app\common\services;


use Ehann\RediSearch\Index;
use Ehann\RediSearch\Suggestion;

class SearchService extends AbstractService
{
    /**
     * Get search data source
     *
     * @param string $instance
     * @return Suggestion|Index
     *
     * @throws \Exception
     */
    public static function getDataSource($instance = 'indexing')
    {
        if ($instance == 'indexing') {
            return \Phalcon\Di::getDefault()->get('categoryCacheIndex');
        } elseif ($instance == 'suggestion') {
            return \Phalcon\Di::getDefault()->get('categoryCacheSuggest');
        } else {
            throw new \Exception('No data source is available');
        }
    }

    /**
     * Autocomplete search
     *
     * @param array $searchParams
     * @return array
     *
     * @throws \Exception
     */
    public function autocomplete(array $searchParams = []): array
    {
        return ['results' => self::getDataSource('suggestion')->get($searchParams['keyword'], true)];
    }

    /**
     * Categories search
     *
     * @param array $searchParams
     * @return array
     *
     * @throws \Ehann\RedisRaw\Exceptions\RedisRawCommandException
     * @throws \Exception
     */
    public function search(array $searchParams = []): array
    {
        return [
            'results' => self::getDataSource()->search($searchParams['keyword'], true)
                ->getDocuments()
        ];
    }
}