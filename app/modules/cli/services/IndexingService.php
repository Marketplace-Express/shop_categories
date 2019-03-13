<?php
/**
 * User: Wajdi Jurry
 * Date: 27/02/19
 * Time: 12:11 م
 */

namespace Shop_categories\Modules\Cli\Services;


use Ehann\RediSearch\Index;
use Ehann\RediSearch\Suggestion;
use Phalcon\Di\Injectable;
use Shop_categories\Enums\CacheIndexesEnum;
use Shop_categories\Redis\DocumentMapper;

class IndexingService extends Injectable
{

    const INDEX_NAME_PREFIX = 'idx:';

    private $indexName = self::INDEX_NAME_PREFIX.CacheIndexesEnum::CATEGORY_INDEX_NAME;

    /** @var Index */
    private $redisIndexing;

    /** @var Suggestion */
    private $redisSuggesting;

    /** @var \Redis */
    private $redis;

    public function __construct()
    {
        $this->redis = $this->getDI()->get('category_cache');
        $this->redisIndexing = $this->getDI()->get('category_cache_index');
        $this->redisSuggesting = $this->getDI()->get('category_cache_suggest');
    }

    /**
     * Create an index
     *
     */
    public function create()
    {
        $this->redisIndexing
            ->addTextField('name')
            ->addTextField('url')
            ->create();
    }

    /**
     * Add document to index
     *
     * @param string $docId
     * @param string $name
     * @param string $url
     *
     * @throws \Ehann\RediSearch\Exceptions\FieldNotInSchemaException
     * @throws \Exception
     */
    public function add(string $docId, string $name, ?string $url)
    {
        if (empty($docId) || empty($name)) {
            throw new \Exception('Missing arguments');
        }
        if (!$this->redis->exists($this->indexName))  {
            $this->create();
        }
        $document = new DocumentMapper($docId);
        $document = $document->makeDocument($name, $url);
        $this->redisIndexing->add($document);
        $this->redisSuggesting->add($name, 1.0);
    }

    /**
     * Delete document
     *
     * @param string $id
     *
     * @throws \Exception
     */
    public function delete(string $id)
    {
        if (empty($id)) {
            throw new \Exception('Missing argument');
        }
        $this->redisIndexing->delete($id);
    }

    /**
     * Drop an index
     *
     * @param array $params
     *
     * @throws \Exception
     */
    public function drop(array $params)
    {
        if (empty($params)) {
            throw new \Exception('Missing arguments');
        }
        $this->redisIndexing->drop();
    }
}