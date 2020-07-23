<?php

namespace MageSuite\ProductsRenderer\Service;

class ProductCategoryResolver
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * @param int $productId
     * @param int|null $storeId
     * @return \Magento\Catalog\Model\Category|null
     */
    public function getCategory($productId, $storeId = null)
    {
        $categoryCollection = $this->categoryCollectionFactory->create();
        $categoryCollection->addIsActiveFilter()
            ->addNameToResult()
            ->addUrlRewriteToResult();

        if ($storeId) {
            $categoryCollection->setStoreId($storeId);
        }

        $categoryCollection->getSelect()->joinInner(
            ['ccp' => $categoryCollection->getTable('catalog_category_product')],
            'ccp.category_id = e.entity_id',
            []
        )->where('ccp.product_id = ?', (int)$productId
        )->limit(1);

        if (!$categoryCollection->count()) {
            return null;
        }

        $category = $categoryCollection->getFirstItem();

        return $category;
    }
}