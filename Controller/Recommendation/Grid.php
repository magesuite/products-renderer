<?php

declare(strict_types=1);

namespace MageSuite\ProductsRenderer\Controller\Recommendation;

class Grid implements \Magento\Framework\App\Action\HttpGetActionInterface
{
    public function __construct(
        protected \MageSuite\ProductsRenderer\Helper\Configuration $configuration,
        protected \Magento\Framework\View\Result\PageFactory $pageFactory,
        protected \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        protected \Magento\Framework\App\RequestInterface $request
    ) {}

    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        $params = $this->request->getParams();
        $data = [];
        $data['useTeaser'] = '';
        $data['rows_desktop'] = '1';
        $data['rows_mobile'] = '4';
        $data['rows_tablet'] = '2';

        if (isset($params['id'])) {
            $data['product_ids'] = $params['id'];
        }

        if (isset($params['skus'])) {
            $data['skus'] = implode(',', $params['skus']);
        }

        if (isset($params['rows_desktop'])) {
            $data['rows_desktop'] = $params['rows_desktop'];
        }

        if (isset($params['rows_tablet'])) {
            $data['rows_tablet'] = $params['rows_tablet'];
        }

        if (isset($params['rows_mobile'])) {
            $data['rows_mobile'] = $params['rows_mobile'];
        }

        $data['limit'] = $this->configuration->getProductLimit();

        if (isset($params['limit'])) {
            $data['limit'] = min($data['limit'], $params['limit']);
        }

        $data['collection_type'] = \MageSuite\ContentConstructorFrontend\DataProviders\ProductCarouselDataProvider::COLLECTION_TYPE_DATABASE;
        $resultPage = $this->pageFactory->create();
        $component = $resultPage
            ->getLayout()
            ->createBlock(
                \MageSuite\ContentConstructorFrontend\Block\Component::class,
                '',
                [
                    'data' => [
                        'type' => 'product-grid',
                        'data' => $data
                    ]
                ]
            )
            ->toHtml();
        $resultJson = $this->jsonFactory->create();

        return $resultJson->setData(['content' => $component]);
    }
}
