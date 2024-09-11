<?php

declare(strict_types=1);

namespace Magentix\FormSelect2\Controller\Adminhtml\Ajax;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\HttpInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Result\PageFactory;

class Search extends Action
{
    protected PageFactory $resultPageFactory;

    protected Json $jsonSerializer;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Json $jsonSerializer
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonSerializer = $jsonSerializer;

        parent::__construct($context);
    }

    public function execute(): HttpInterface
    {
        try {
            $id = $this->getRequest()->getParam('id');
            $query = $this->getRequest()->getParam('q');
            $page = $this->getRequest()->getParam('page', 1);

            $search = $this->_objectManager->create(
                'Magentix\\FormSelect2\\Model\\Search\\' . ucfirst(strtolower($this->getRequest()->getParam('model', '')))
            );

            $items = [];

            if ($query) {
                $items = $search->getList($query, $page);
            }

            if ($id) {
                $items = $search->get($id);
            }

            if ($query || $id) {
                $response = [
                    'query' => $query,
                    'size' => count($items),
                    'page' => $page,
                    'items' => $items,
                    'error' => false,
                ];

                return $this->jsonResponse($response);
            }

            return $this->jsonResponse();
        } catch (Exception $e) {
            return $this->jsonResponse(
                [
                    'items' => [],
                    'error' => $e->getMessage(),
                ]
            );
        }
    }

    protected function jsonResponse(array $response = []): HttpInterface
    {
        return $this->getResponse()->representJson(
            $this->jsonSerializer->serialize($response)
        );
    }
}
