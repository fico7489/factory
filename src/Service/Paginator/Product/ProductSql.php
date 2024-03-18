<?php

namespace App\Service\Paginator\Product;

use App\Entity\User;

class ProductSql
{
    public function prepareSqlSort(array $sorts): string
    {
        if (0 === count($sorts)) {
            $sorts[] = ['price' => 'asc'];
        }

        $sqlSort = [];
        foreach ($sorts as $value) {
            $sortName = array_key_first($value);
            $sortType = $value[$sortName];

            $sqlSort[] = $sortName.' '.$sortType;
        }

        $sqlSort = ' ORDER BY '.implode(',', $sqlSort);

        return $sqlSort;
    }

    public function preparePriceFilter(array $filters, array &$sqlParams): string
    {
        if (!empty($value = $filters[0]['price']['lte'] ?? null)) {
            $sqlParams['price'] = $value;

            return ' HAVING price_adjusted < :price';
        }

        return '';
    }

    public function prepareCategoryFilter(array $filters, array &$sqlParams): string
    {
        if (!empty($value = $filters[0]['category']['equals'] ?? null)) {
            $sqlParams['category'] = $value;

            return '        AND EXISTS (
                SELECT * FROM product_category pc where pc.category_id in (:category) and pc.product_id = p.id
            ) ';
        }

        return '';
    }

    public function prepareNameFilter(array $filters, array &$sqlParams): string
    {
        if (!empty($value = $filters[0]['name']['starts_with'] ?? null)) {
            $sqlParams['name'] = $value.'%';

            return ' AND name LIKE :name  ';
        }

        return '';
    }

    public function prepareUserGroupIds(User $user): string
    {
        $userGroups = $user->getUserGroups()->toArray();

        $userGroupIds = [-1];
        foreach ($userGroups as $userGroup) {
            $userGroupIds[] = $userGroup->getId();
        }
        $userGroupIds = implode(',', $userGroupIds);

        return $userGroupIds;
    }
}
