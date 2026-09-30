<?php

namespace Dept\RendererImporter\Model\Resolver;

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\ResourceConnection;

class RendererMetaResolver implements ResolverInterface
{
    protected $productRepository;
    private ResourceConnection $resourceConnection;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ResourceConnection $resourceConnection
    ) {
        $this->productRepository = $productRepository;
        $this->resourceConnection = $resourceConnection;

    }

    public function resolve(
        $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        $sku = $value['sku'] ?? null;
        if (!$sku) {
            return null;
        }
        try {
            $connection = $this->resourceConnection->getConnection();
            $tableName = $this->resourceConnection->getTableName('custom_shade_to_renderer_mapping_tbl');
            $select = $connection->select()
            ->from($tableName)
            ->where('sku = ?', $sku);

            $record = $connection->fetchRow($select);

            if($record) {
                return $record['renderer_meta_json'];
            }

        } catch (\Exception $e) {
            return null;
        }
        return '{}';
    }
}
