<?php
/**
 * User: Wajdi Jurry
 * Date: 02/03/19
 * Time: 05:29 م
 */

namespace Shop_categories\Services;


class SearchService extends AbstractService
{
    /**
     * Get search data source
     *
     * @return \Shop_categories\Repositories\CategoryRepository|Cache\CategoryCache
     * @throws \Exception
     *
     */
    public static function getDataSource()
    {
        try {
            return parent::getCategoryCache();
        } catch (\RedisException $exception) {
            return parent::getCategoryRepository();
        } catch (\Throwable $exception) {
            throw new \Exception($exception->getMessage() ?: 'No data source available for search');
        }
    }

    /**
     * Search for categories by keyword
     *
     * @param array $searchParams
     * @return array
     *
     * @throws \Exception
     */
    public function autocomplete(array $searchParams = []): array
    {
        return self::getDataSource()->autoComplete($searchParams['keyword']);
    }
}