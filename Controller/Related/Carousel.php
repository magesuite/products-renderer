<?php

declare(strict_types=1);

namespace MageSuite\ProductsRenderer\Controller\Related;

class Carousel implements \Magento\Framework\App\Action\HttpGetActionInterface
{
    public function __construct(
        protected \MageSuite\ProductsRenderer\Helper\Configuration $configuration,
        protected \MageSuite\ProductsRenderer\Service\RelatedProductsResolver $relatedProductsResolver,
        protected \MageSuite\ProductsRenderer\Service\ProductCategoryResolver $productCategoryResolver,
        protected \Magento\Framework\App\RequestInterface $request,
        protected \Magento\Framework\View\Result\PageFactory $pageFactory,
        protected \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    ) {}

    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        $resultJson = $this->jsonFactory->create();
        $relatedProductIds = $this->getRelatedProductIds();

        if (empty($relatedProductIds)) {
            return $resultJson->setData(['content' => '', 'category' => []]);
        }

        $category = null;
        $data = ['product_ids' => null];

        if (!empty($relatedProductIds)) {
            $data['product_ids'] = implode(',', $relatedProductIds);
            $category = $this->getCategory($relatedProductIds);
        }

        $data['limit'] = $this->configuration->getProductLimit();
        $data['collection_type'] = \MageSuite\ContentConstructorFrontend\DataProviders\ProductCarouselDataProvider::COLLECTION_TYPE_DATABASE;

        $resultPage = $this->pageFactory->create();
        $component = $resultPage
            ->getLayout()
            ->createBlock(
                \MageSuite\ContentConstructorFrontend\Block\Component::class,
                '',
                [
                    'data' => [
                        'type' => 'product-carousel',
                        'data' => $data
                    ]
                ]
            )
            ->toHtml();

        return $resultJson->setData(['content' => $component, 'category' => $category]);
    }

    protected function getRelatedProductIds(): ?array
    {
        $id = (int)$this->request->getParam('id');
        $relationType = (string)$this->request->getParam('relation_type');

        return $this->relatedProductsResolver->getRelatedProductIds(
            $id,
            $relationType
        );
    }

    protected function getCategory(array $productIds): ?array
    {
        if (empty($productIds)) {
            return null;
        }

        $productId = reset($productIds);
        $category = $this->productCategoryResolver->getCategory($productId);

        if ($category) {
            return [
                'url' => $category->getUrl(),
                'name' => $category->getName()
            ];
        }

        return null;
    }
}
