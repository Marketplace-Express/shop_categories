<?php
/**
 * User: Wajdi Jurry
 * Date: 23/02/19
 * Time: 12:36 م
 */

namespace Shop_categories\Modules\Cli\Tasks;


use Ehann\RediSearch\Index;
use Shop_categories\Enums\CacheIndexesEnum;
use Shop_categories\Redis\DocumentMapper;

class IndexTask extends MainTask
{

    /** @var Index */
    private $redisIndexing;

    public function onConstruct()
    {
        $this->redisIndexing = $this->getDI()->get('category_cache_index');
    }

    /**
     * Create an index
     *
     */
    public function create()
    {
        $this->redisIndexing->setIndexName(CacheIndexesEnum::CATEGORY_INDEX_NAME)
            ->addTextField('title')
            ->addTextField('url')
            ->addTextField('id')
            ->create();
    }

    /**
     * Add document to index
     *
     * @param array $params
     *
     * @throws \Exception
     */
    public function addAction(array $params)
    {
        if (empty($params)) {
            throw new \Exception('Missing arguments');
        }
        if (!$this->redisIndexing->setIndexName(CacheIndexesEnum::CATEGORY_INDEX_NAME)->info()) {
            $this->create();
        }
        $document = new DocumentMapper($params['id']);
        $document = $document->makeDocument($params['title'], $params['url']);
        $this->redisIndexing->setIndexName(CacheIndexesEnum::CATEGORY_INDEX_NAME)
            ->add($document);
    }

    /**
     * Delete document
     *
     * @param string $id
     *
     * @throws \Exception
     */
    public function deleteAction(string $id)
    {
        if (empty($id)) {
            throw new \Exception('Missing argument');
        }
        $this->redisIndexing->setIndexName(CacheIndexesEnum::CATEGORY_INDEX_NAME)
            ->delete($id);
    }

    /**
     * Drop an index
     *
     * @param array $params
     *
     * @throws \Exception
     */
    public function dropAction(array $params)
    {
        if (empty($params)) {
            throw new \Exception('Missing arguments');
        }
        $this->redisIndexing->setIndexName(CacheIndexesEnum::CATEGORY_INDEX_NAME)
            ->drop();
    }
}