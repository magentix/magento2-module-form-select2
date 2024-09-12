<?php

declare(strict_types=1);

namespace Magentix\FormSelect2\Model;

use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

class Search
{
    public function __construct(
        protected array $searchFields,
        protected AbstractModel $modelClass,
        protected AbstractCollection $modelCollectionClass,
        protected string $modelType,
        protected string $modelKey,
        protected ?string $sortBy = null
    ) {
    }

    /**
     * @throws LocalizedException
     */
    public function getList(string $query, int $page): array
    {
        $collection = $this->modelCollectionClass;

        $conditions = [];
        $eavFilters = [];

        foreach ($this->searchFields as $searchField) {
            $conditions[] = ['like' => '%' . $query . '%'];
            $eavFilters[] = ['attribute' => $searchField, 'like'=>'%' . $query . '%'];
        }

        if ($this->modelType === 'eav') {
            $collection->addAttributeToFilter($eavFilters, null, 'left');
        } else {
            $collection->addFieldToFilter($this->searchFields, [$conditions]);
        }

        if ($page) {
            $collection->setPageSize(20);
            $collection->setCurPage($page);
        }

        if ($this->sortBy) {
            $collection->setOrder($this->sortBy, 'ASC');
        }

        $items = [];

        foreach($collection as $item) {
            $items[] = ['id' => $item->getData($this->modelKey), 'text' => $this->getItemText($item)];
        }

        return $items;

    }

    public function get(string $key): array
    {
        $model = $this->modelClass->load($key, $this->modelKey);

        $items[] = ['id' => $model->getData($this->modelKey), 'text' => $this->getItemText($model)];

        return $items;
    }

    public function getItemText(DataObject $item): string
    {
        $fields = $this->searchFields;
        $text = [];

        foreach($fields as $field) {
            $text[] = $item->getData($field);
        }

        return join(' ', $text);
    }
}
