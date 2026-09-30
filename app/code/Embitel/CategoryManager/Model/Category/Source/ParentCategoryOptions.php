<?php

namespace Embitel\CategoryManager\Model\Category\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\Category as CategoryModel;

class ParentCategoryOptions implements OptionSourceInterface
{
    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var array
     */
    protected $categoriesTree;

    /**
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param RequestInterface $request
     */
    public function __construct(
        CategoryCollectionFactory $categoryCollectionFactory,
        RequestInterface $request
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->request = $request;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return $this->getCategoriesTree();
    }

    /**
     * Retrieve categories tree
     *
     * @return array
     */
    protected function getCategoriesTree()
    {
        if ($this->categoriesTree === null) {
            $storeId = $this->request->getParam('store');
            
            $collection = $this->categoryCollectionFactory->create();
            $collection->addAttributeToSelect(['name', 'is_active', 'parent_id', 'path'])
                ->addAttributeToFilter('entity_id', ['neq' => CategoryModel::TREE_ROOT_ID])
                ->setStoreId($storeId)
                ->setOrder('level', 'ASC')
                ->setOrder('position', 'ASC');

            $categoryById = [
                CategoryModel::TREE_ROOT_ID => [
                    'value' => (string)CategoryModel::TREE_ROOT_ID,
                    'label' => __('Root')
                ],
            ];

            foreach ($collection as $category) {
                $categoryId = (string)$category->getId();
                $parentId = (string)$category->getParentId();

                foreach ([$categoryId, $parentId] as $id) {
                    if (!isset($categoryById[$id])) {
                        $categoryById[$id] = ['value' => $id];
                    }
                }

                $categoryById[$categoryId]['is_active'] = $category->getIsActive();
                $categoryById[$categoryId]['label'] = (string)$category->getName();
                
                $categoryById[$parentId]['optgroup'][] = &$categoryById[$categoryId];
            }

            // Fallback if root tree index is missing or empty
            $this->categoriesTree = $categoryById[CategoryModel::TREE_ROOT_ID]['optgroup'] ?? [];
        }

        return $this->categoriesTree;
    }
}
