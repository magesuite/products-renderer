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
     * @var \MageSuite\ProductsRenderer\Service\ProductCategoryResolver
     */
    protected $productCategoryResolver;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \MageSuite\ProductsRenderer\Service\RelatedProductsResolver $relatedProductsResolver,
        \MageSuite\ProductsRenderer\Service\ProductCategoryResolver $productCategoryResolver
    ) {
        parent::__construct($context);

        $this->context = $context;
        $this->pageFactory = $pageFactory;
        $this->jsonFactory = $jsonFactory;
        $this->relatedProductsResolver = $relatedProductsResolver;
        $this->productCategoryResolver = $productCategoryResolver;
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
        $category = null;
        $data = [];

        if (!empty($relatedProductIds)) {
            $data['product_ids'] = implode(',', $relatedProductIds);
            $category = $this->getCategory($relatedProductIds);
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

        return $resultJson->setData(['content' => $component, 'category' => $category]);
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

    protected function getCategory(array $productIds)
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
