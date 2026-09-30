<?php
namespace TeknoPoint\GraphqlCustomization\Model;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{

    const GQL_Attribution_Configuration = 'teknopoint_config/gql_customization_configuration/custom_attribute';

    /**
     * @return array
     */
    public function getGqlAttribution():array
    {
      $data = $this->scopeConfig->getValue(self::GQL_Attribution_Configuration, ScopeInterface::SCOPE_STORE);
       return $this->getAttributeCode($data);
    }

    /**
     * @param string $data
     * @return array
     */
    private function getAttributeCode(string $data):array
    {
        $store = [];
        $array = explode(",",$data);
        foreach ($array as $item){
            $store[] = trim($item);
        }
        return $store;
    }

}


