<?php

declare(strict_types=1);

namespace MageSuite\ProductsRenderer\Controller\Recommendation;

class Carousel implements \Magento\Framework\App\Action\HttpGetActionInterface
{
    public function __construct(
        protected \MageSuite\ProductsRenderer\Helper\Configuration $configuration,
        protected \Magento\Framework\App\RequestInterface $request,
        protected \Magento\Framework\View\Result\PageFactory $pageFactory,
        protected \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    ) {}

    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        $params = $this->request->getParams();
        $data = [
            'limit' => $this->configuration->getProductLimit(),
            'collection_type' => \MageSuite\ContentConstructorFrontend\DataProviders\ProductCarouselDataProvider::COLLECTION_TYPE_DATABASE
        ];

        if (isset($params['id'])) {
            $data['product_ids'] = $params['id'];
        }

        if (isset($params['skus'])) {
            $data['skus'] = implode(',', $params['skus']);
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
