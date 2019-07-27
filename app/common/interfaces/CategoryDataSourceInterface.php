<?php
/**
 * User: Wajdi Jurry
 * Date: 28/12/18
 * Time: 12:15 م
 */

namespace app\common\interfaces;


interface CategoryDataSourceInterface
{
    public function getCategory(string $categoryId, string $vendorId);

    public function getRoots(string $vendorId): array;

    public function getChildren(string $categoryId, string $vendorId): array;

    public function getDescendants(string $categoryId, string $vendorId): array;

    public function getParents(string $categoryId, string $vendorId): array;

    public function getParent(string $categoryId, string $vendorId);

    public function getAll(string $vendorId): array;
}