<?php

namespace MageSuite\ProductsRenderer\Controller\Related;

class Carousel extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\Action\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pageFactory;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;
    /**
     * @var \MageSuite\ProductsRenderer\Service\RelatedProductsResolver
     */
    protected $relatedProductsResolver;

    /**
     * @var \MageSuite\ProductsRenderer\Service\ProductCategoryUrlResolver
     */
    protected $productCategoryUrlResolver;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \MageSuite\ProductsRenderer\Service\RelatedProductsResolver $relatedProductsResolver,
        \MageSuite\ProductsRenderer\Service\ProductCategoryUrlResolver $productCategoryUrlResolver
    ) {
        parent::__construct($context);

        $this->context = $context;
        $this->pageFactory = $pageFactory;
        $this->jsonFactory = $jsonFactory;
        $this->relatedProductsResolver = $relatedProductsResolver;
        $this->productCategoryUrlResolver = $productCategoryUrlResolver;
    }

    /**
     * Action renders product carousel based on provided skus
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $relatedProductIds = $this->getRelatedProductIds();
        $categoryUrl = $this->getCategoryUrl($relatedProductIds);
        $data = [];

        if (!empty($relatedProductIds)) {
            $data['product_ids'] = implode(',', $relatedProductIds);
        }

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
        $resultJson = $this->jsonFactory->create();

        return $resultJson->setData(['content' => $component, 'category_url' => $categoryUrl]);
    }

    protected function getRelatedProductIds()
    {
        $id = (string)$this->getRequest()->getParam('id');
        $relationType = (string)$this->getRequest()->getParam('relation_type');

        return $this->relatedProductsResolver->getRelatedProductIds(
            $id,
            $relationType
        );
    }

    protected function getCategoryUrl(array $productIds)
    {
        if (empty($productIds)) {
            return null;
        }

        $productId = reset($productIds);

        return $this->productCategoryUrlResolver->getCategoryUrl($productId);
    }
}
