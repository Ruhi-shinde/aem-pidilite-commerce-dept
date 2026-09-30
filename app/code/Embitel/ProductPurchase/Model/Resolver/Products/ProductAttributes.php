<?php
/**
 * Copyright © Embitel, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);
namespace Embitel\ProductPurchase\Model\Resolver\Products;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Exception\InputException;
use Magento\Catalog\Model\ProductRepository;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;

/**
 * Get custom attributes
 */
class ProductAttributes implements ResolverInterface
{
    /**
     * @param ProductRepository $productRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        protected ProductRepository $productRepository,
        private StoreManagerInterface $storeManager,
    ) {
    }

    /**
     * Fetching custom attribute option name from option value
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     *
     * @return string|array
     *
     * @throws GraphQlInputException
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }
        $product = $value['model'];
        try {
            $mediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
            $products = $this->productRepository->getById($product->getId());
            $optionText = $product->getResource()->getAttribute('product_pdf')->getFrontend()->getValue($products);
            if ($optionText) {
                $optionText = $mediaUrl.$optionText;
            }
            return $optionText;
        } catch (InputException $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }
    }
}
