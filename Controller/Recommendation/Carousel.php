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

        if(!isset($params['id']) or empty($params['id']) or !is_array($params['id'])) {
            throw new \Magento\Framework\Exception\NotFoundException(__(''));
        }

        $ids = $params['id'];

        $resultPage = $this->pageFactory->create();

        $component = $resultPage
            ->getLayout()
            ->createBlock(
                \MageSuite\ContentConstructorFrontend\Block\Component::class, 'nosto-carousel', [
                    'data' => [
                        'type' => 'product-carousel',
                        'data' => ['product_ids' => $ids]
                    ]
                ]
            )
            ->toHtml();

        $this->getResponse()->setBody($component);
    }
}
