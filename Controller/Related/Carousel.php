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

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \MageSuite\ProductsRenderer\Service\RelatedProductsResolver $relatedProductsResolver
    ) {
        parent::__construct($context);

        $this->context = $context;
        $this->pageFactory = $pageFactory;
        $this->jsonFactory = $jsonFactory;
        $this->relatedProductsResolver = $relatedProductsResolver;
    }

    /**
     * Action renders product carousel based on provided skus
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        $data = [];
        if (isset($params['id']) && isset($params['relation_type'])) {
            $data['product_ids'] = $this->relatedProductsResolver->getRelatedProductIds($params['id'], $params['relation_type']);
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
        return $resultJson->setData(['content' => $component]);
    }
}
