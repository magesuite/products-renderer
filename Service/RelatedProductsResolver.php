<?php

namespace MageSuite\ProductsRenderer\Service;

class RelatedProductsResolver
{
    protected $relationTypeMap = [
        'related' => 'getRelatedProductIds',
        'crosssell' => 'getCrossSellProductIds',
        'upsell' => 'getUpSellProductIds'
    ];

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
    }

    protected function initProduct($productId)
    {
        if (!$productId) {
            return false;
        }

        $storeId = $this->storeManager->getStore()->getId();
        
        try {
            return $this->productRepository->getById($productId, false, $storeId);
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    public function getRelatedProductIds($productId, $relationType)
    {
        $product = $this->initProduct($productId);

        if (empty($product) || !isset($this->relationTypeMap[$relationType])) {
            return null;
        }

        $relationMethod = $this->relationTypeMap[$relationType];
        $relationProductIds = $product->$relationMethod();

        if (!empty($relationProductIds)) {
            return $relationProductIds;
        }

        return null;
    }
}
