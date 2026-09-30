<?php

namespace TeknoPoint\GraphqlCustomization\Model\Resolver\Product;

use Zend_Log;
use Zend_Log_Exception;
use Zend_Log_Writer_Stream;
use Magento\Setup\Exception;
use Magento\Catalog\Model\ProductRepository;
use TeknoPoint\GraphqlCustomization\Model\Config;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Query\ResolverInterface;

class ProductAttributionResolver implements ResolverInterface
{

    private ProductRepository $productRepository;
    private Config $config;
    private Zend_Log $logger;

    /**
     * @throws Zend_Log_Exception
     */
    public function __construct(ProductRepository $productRepository, Config $config)
    {
        $this->productRepository = $productRepository;
        $this->config = $config;
        $writer = new Zend_Log_Writer_Stream(BP . '/var/log/pidilite-website-search.log');
        $this->logger = new Zend_Log();
        $this->logger->addWriter($writer);
    }

    /**
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     */
    public function resolve(Field $field, $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        $storeAttribute = [];
        try {
            $productRepo = $this->productRepository->get($value['sku']);
            $attributes = $productRepo->getCustomAttributes();
            $customAttributes = $this->config->getGqlAttribution();
            if(!empty($customAttributes) && !empty($attributes)) {
                foreach ($attributes as $attribute) {
                    if (in_array($attribute->getAttributeCode(), $customAttributes)) {
                        $isTextField = empty($productRepo->getResource()
                            ->getAttribute($attribute->getAttributeCode())->getOptions());

                        if($isTextField) {
                            $storeAttribute[] = ["code" => $attribute->getAttributeCode() , "value" => $attribute->getValue()];
                        } else {
                            $optionText = $productRepo->getResource()->getAttribute($attribute->getAttributeCode())
                                ->getSource()->getOptionText($attribute->getValue());
                            $storeAttribute[] = ["code" => $attribute->getAttributeCode() , "value" => $optionText];
                        }
                    }
                }
                $this->logger->info("Pidilite Custom Attributes are:" .json_encode($storeAttribute));
                return $storeAttribute;
            }else{
                $this->logger->err("Something went wrong in Attributes Data:" .json_encode($customAttributes));
            }
        } catch (\Exception $error) {
            $this->logger->err("Error Occurring in ProductAttribution resolve() " . $error->getMessage());
        }
        return $storeAttribute;
    }

}
