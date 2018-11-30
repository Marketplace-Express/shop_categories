<?php
/**
 * User: Wajdi Jurry
 * Date: 28/09/18
 * Time: 05:57 م
 */
namespace Shop_categories\Models\Behaviors;

interface BaseBehavior
{
    public function roots(bool $toArray = true);

    public function parents(string $itemId, bool $toArray = true, $recursive = true, bool $oneParent = false, bool $addSelf = false);

    public function descendants(string $itemId, bool $toArray = true, $recursive = true);

    public function children(string $itemId, bool $toArray = true);

    public function cascadeDelete(string $itemId);
}