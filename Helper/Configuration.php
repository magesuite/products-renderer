<?php

declare(strict_types=1);

namespace MageSuite\ProductsRenderer\Helper;

class Configuration
{
    public const XML_PATH_PRODUCT_LIMIT = 'catalog/frontend/grid_per_page';

    public function __construct(
        protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {}

    public function getProductLimit(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_LIMIT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
