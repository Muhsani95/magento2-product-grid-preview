<?php

namespace GM\ProductGridPreview\Ui\Component\Columns;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class PreviewColumn extends Column
{
    protected $productRepository;
    protected $storeManager;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $product = $this->productRepository->getById($item['entity_id']);
                $storeViews = $this->storeManager->getStores(true);
                $links = [];

                foreach ($storeViews as $store) {
                    $url = $product->getUrlModel()->getUrlInStore($product, ['_store' => $store]);
                    $links[] = '<a target="_blank" href="' . $url . '">' . $store->getName() . '</a>';
                }

                $item['preview'] = implode(' | ', $links);
            }
        }

        return $dataSource;
    }
}
