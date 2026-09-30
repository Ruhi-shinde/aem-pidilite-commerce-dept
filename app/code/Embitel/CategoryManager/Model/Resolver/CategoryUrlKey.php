<?php
/**
 * Copyright © Embitel, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Embitel\CategoryManager\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class CategoryUrlKey implements ResolverInterface
{
    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        if (isset($value['model']) && method_exists($value['model'], 'getUrlKey')) {
            return $value['model']->getUrlKey();
        }
        return $value['url_key'] ?? ($value['slug'] ?? null);
    }
}
