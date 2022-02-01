<?php

namespace MageSuite\ProductsRenderer\Controller\Recommendation;

class Grid extends \Magento\Framework\App\Action\Action
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

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    ) {
        parent::__construct($context);

        $this->context = $context;
        $this->pageFactory = $pageFactory;
        $this->jsonFactory = $jsonFactory;
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

        if (isset($params['limit'])) {
            $data['limit'] = $params['limit'];
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
