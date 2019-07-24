<?php

namespace MageSuite\ProductsRenderer\Controller\Recommendation;

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

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    )
    {
        parent::__construct($context);

        $this->context = $context;
        $this->pageFactory = $pageFactory;
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
        if(isset($params['id'])) {
            $data['product_ids'] = $params['id'];
        }

        if(isset($params['skus'])) {
            $data['skus'] =  implode(',', $params['skus']);
        }

        $resultPage = $this->pageFactory->create();

        $component = $resultPage
            ->getLayout()
            ->createBlock(
                \MageSuite\ContentConstructorFrontend\Block\Component::class, '', [
                    'data' => [
                        'type' => 'product-carousel',
                        'data' => $data
                    ]
                ]
            )
            ->toHtml();

        $this->getResponse()->setBody($component);
    }
}
