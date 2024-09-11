<?php

declare(strict_types=1);

namespace Magentix\FormSelect2\Model;

use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\ObjectManagerInterface;

abstract class Search
{
    protected ObjectManagerInterface $objectManager;

    public function __construct(
        ObjectManagerInterface $objectManager,
    ) {
        $this->objectManager = $objectManager;
    }

    private function getModel(): AbstractModel
    {
        return $this->objectManager->create($this->modelClass);
    }

    public function getCollectionModel(): AbstractCollection
    {
        return $this->objectManager->create($this->modelCollectionClass);
    }

    /**
     * @throws LocalizedException
     */
    public function getList(string $query, int $page): array
    {
        $collection = $this->getCollectionModel();
        $searchFields = $this->searchFields;

        $conditions = [];
        $eavFilters = [];

        foreach ($searchFields as $searchField) {
            $conditions[] = ['like' => '%' . $query . '%'];
            $eavFilters[] = ['attribute' => $searchField, 'like'=>'%' . $query . '%'];
        }

        if ($this->modelType === 'eav') {
            $collection->addAttributeToFilter($eavFilters, null, 'left');
        } else {
            $collection->addFieldToFilter($searchFields, [$conditions]);
        }

        if ($page) {
            $collection->setPageSize(20);
            $collection->setCurPage($page);
        }

        if ($this->sortBy){
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
        $model = $this->getModel()->load($key, $this->modelKey);

        $items[] = ['id' => $model->getData($this->modelKey), 'text' => $this->getItemText($model)];

        return $items;
    }

    protected function getItemText(DataObject $item): string
    {
        $fields = $this->searchFields;
        $text = [];

        foreach($fields as $field){
            $text[] = $item->getData($field);
        }

        return join(' ', $text);
    }
}
