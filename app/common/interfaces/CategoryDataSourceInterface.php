<?php
/**
 * User: Wajdi Jurry
 * Date: 28/12/18
 * Time: 12:15 م
 */

namespace app\common\interfaces;


interface CategoryDataSourceInterface
{
    public function getCategory(string $categoryId, string $storeId);

    public function getRoots(string $storeId): array;

    public function getChildren(string $categoryId, string $storeId): array;

    public function getDescendants(string $categoryId, string $storeId): array;

    public function getParents(string $categoryId, string $storeId): array;

    public function getParent(string $categoryId, string $storeId);

    public function getAll(string $storeId): array;
}