<?php
/**
 * User: Wajdi Jurry
 * Date: 23/02/19
 * Time: 06:35 م
 */

namespace app\common\redis;


use Ehann\RediSearch\Document\Document;
use Ehann\RediSearch\Fields\TextField;
use app\common\utils\UuidUtil;

class DocumentMapper extends Document
{
    /** @var null|string */
    protected $id;

    /** @var TextField */
    public $storeId;

    /** @var TextField|$this */
    public $name;

    /** @var TextField|$this */
    public $url;

    /**
     * DocumentMapper constructor.
     * @param string $id
     * @throws \Exception
     */
    public function __construct(string $id)
    {
        if (empty($id) || !(new UuidUtil())->isValid($id)) {
            throw new \Exception('Invalid document id');
        }
        $this->id = $id;
        parent::__construct($id);
    }

    /**
     * @param string $storeId
     * @param string $name
     * @param string|null $url
     * @return $this
     */
    public function makeDocument(string $storeId, string $name, ?string $url)
    {
        $this->storeId = new TextField('categoryStoreId', $storeId);
        $this->name = new TextField('categoryName', $name);
        $this->url = new TextField('categoryUrl', $url);
        return $this;
    }
}