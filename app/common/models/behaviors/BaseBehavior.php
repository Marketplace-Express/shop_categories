<?php
/**
 * User: Wajdi Jurry
 * Date: 28/09/18
 * Time: 05:57 م
 */
namespace Shop_categories\Models\Behaviors;

interface BaseBehavior
{
    public function roots(array $additionalConditions = []);

    public function parents(string $itemId, array $additionalConditions = [], bool $oneParent = false, bool $addSelf = false);

    public function descendants(string $itemId, array $additionalConditions = []);

    public function children(string $itemId, array $additionalConditions = []);

    public function deleteCascade(string $itemId, array $additionalConditions = []);
}