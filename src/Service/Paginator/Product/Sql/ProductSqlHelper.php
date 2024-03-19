<?php

namespace App\Service\Paginator\Product\Sql;

use App\Entity\User;

// just one helper to move some logic
class ProductSqlHelper
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

    public function prepareSelect(User $user): string
    {
        // prepare price where
        $userId = $user->getUserIdentifier();
        $userGroupIds = $this->prepareUserGroupIds($user);

        $sqlSelect = '
                SELECT
            *,
             (
                    select min(price) from (
                        (SELECT p.price)
                        UNION
                        (SELECT min(pcl.price) as price FROM product_contract_list pcl WHERE pcl.sku = p.sku AND pcl.user_id = '.$userId.')
                        UNION
                        (SELECT min(ppl.price) as price FROM product_price_list ppl WHERE p.sku = ppl.sku AND ppl.user_group_id in ('.$userGroupIds.'))
                    ) as price_adjusted
                ) as price_adjusted
        ';

        return $sqlSelect;
    }

    private function prepareUserGroupIds(User $user): string
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
