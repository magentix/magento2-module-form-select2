<?php

declare(strict_types=1);

namespace Magentix\FormSelect2\Model\Search;

use Magentix\FormSelect2\Model\Search;

class Customer extends Search
{
    protected array $searchFields = ['email'];

    protected string $modelClass = 'Magento\Customer\Model\Customer';

    protected string $modelCollectionClass = 'Magento\Customer\Model\ResourceModel\Customer\Collection';

    protected string $modelType = 'eav';

    protected string $modelKey = 'entity_id';

    protected string $sortBy = 'email';
}
