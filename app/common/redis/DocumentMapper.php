<?php
/**
 * User: Wajdi Jurry
 * Date: 23/02/19
 * Time: 06:35 م
 */

namespace Shop_categories\Redis;


use Ehann\RediSearch\Document\Document;
use Ehann\RediSearch\Fields\TextField;
use Shop_categories\Utils\UuidUtil;

class DocumentMapper extends Document
{
    /** @var null|string */
    protected $id;

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
     * @param string $name
     * @param string|null $url
     * @return $this
     */
    public function makeDocument(string $name, ?string $url)
    {
        $this->name = new TextField('name', $name);
        $this->url = new TextField('url', $url);
        return $this;
    }
}