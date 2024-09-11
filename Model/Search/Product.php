<?php

declare(strict_types=1);

namespace Magentix\FormSelect2\Model\Search;

use Magentix\FormSelect2\Model\Search;

class Product extends Search
{
    protected array $searchFields = ['name', 'sku'];

    protected string $modelClass = 'Magento\Catalog\Model\Product';

    protected string $modelCollectionClass = 'Magento\Catalog\Model\ResourceModel\Product\Collection';

    protected string $modelType = 'eav';

    protected string $modelKey = 'entity_id';

    protected string $sortBy = 'name';
}
