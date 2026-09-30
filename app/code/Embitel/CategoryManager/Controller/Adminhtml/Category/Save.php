<?php
namespace Embitel\CategoryManager\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Store\Model\StoreManagerInterface;

class Save extends Action
{
    public const ADMIN_RESOURCE = 'Embitel_CategoryManager::categories';

    /** @var CategoryFactory */
    private $categoryFactory;

    /** @var CategoryRepositoryInterface */
    private $categoryRepository;

    /** @var ImageUploader */
    private $imageUploader;

    /** @var StoreManagerInterface */
    private $storeManager;

    /**
     * Initialize the Save controller with necessary dependencies.
     *
     * @param Context $context
     * @param CategoryFactory $categoryFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ImageUploader $imageUploader
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        CategoryFactory $categoryFactory,
        CategoryRepositoryInterface $categoryRepository,
        ImageUploader $imageUploader,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
        $this->imageUploader = $imageUploader;
        $this->storeManager = $storeManager;
    }

    /**
     * Execute the action to save a category in the admin panel.
     *
     * @return Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (isset($data['data']) && is_array($data['data'])) {
            $data = array_replace($data, $data['data']);
            unset($data['data']);
        }
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if (empty($data)) {
            return $resultRedirect->setPath('*/*/index');
        }

        try {
            $categoryId = !empty($data['entity_id']) ? (int) $data['entity_id'] : 0;
            $category = $this->categoryFactory->create()->setStoreId(0);

            if ($categoryId) {
                $category->load($categoryId);
            }

            $parentId = !empty($data['parent_id']) ? (int) $data['parent_id'] : 2;
            $category->setName($data['name'] ?? '');
            $category->setParentId($parentId);
            $category->setIsActive($this->toBoolean($data['is_active'] ?? false));
            $category->setIsAnchor(0);

            if (!empty($data['url_key'])) {
                $category->setUrlKey($data['url_key']);
            }

            if (array_key_exists('description', $data)) {
                $category->setData('description', $this->normalizeDescription($data['description']));
            }

            if (array_key_exists('image', $data)) {
                $image = $data['image'];
                if (is_array($image) && !empty($image[0]['delete'])) {
                    $category->setData('image', null);
                } elseif (is_array($image) && !empty($image[0]['tmp_name'])) {
                    $imageName = $image[0]['name'] ?? $image[0]['file'] ?? '';
                    if ($imageName) {
                        $storedPath = $this->imageUploader->moveFileFromTmp($imageName, true);
                        $category->setData('image', basename($storedPath));
                    }
                } elseif (is_array($image) && !empty($image[0]['name'])) {
                    $category->setData('image', basename($image[0]['name']));
                } else {
                    $category->setData('image', is_string($image) ? $image : null);
                }
            }

            if (array_key_exists('category_banner', $data)) {
                $banner = $data['category_banner'];
                if (is_array($banner) && !empty($banner[0]['delete'])) {
                    $category->setData('category_banner', null);
                } elseif (is_array($banner) && !empty($banner[0]['tmp_name'])) {
                    $bannerName = $banner[0]['name'] ?? $banner[0]['file'] ?? '';
                    if ($bannerName) {
                        $storedPath = $this->imageUploader->moveFileFromTmp($bannerName, true);
                        $category->setData('category_banner', basename($storedPath));
                    }
                } elseif (is_array($banner) && !empty($banner[0]['name'])) {
                    $category->setData('category_banner', basename($banner[0]['name']));
                } else {
                    $category->setData('category_banner', is_string($banner) ? $banner : null);
                }
            }

            if (array_key_exists('include_in_menu', $data)) {
                $category->setIncludeInMenu(!empty($data['include_in_menu']) ? 1 : 0);
            }
            $isStandard = !empty($data['is_standard']) ? 1 : 0;
            $ancestorId = $parentId;
            $visitedAncestorIds = [];

            if ($parentId > 2) {

                while ($ancestorId > 2 && !isset($visitedAncestorIds[$ancestorId])) {
                    $visitedAncestorIds[$ancestorId] = true;
                    $ancestorCategory = $this->categoryFactory->create()->load($ancestorId);
                    $ancestorIsStandard = $ancestorCategory->getData('is_standard');

                    if ($ancestorIsStandard === null) {
                        break;
                    }

                    if ((int) $ancestorIsStandard !== 1) {
                        $isStandard = false;
                        break;
                    }
                    $isStandard = true;

                    $ancestorId = (int) $ancestorCategory->getParentId();
                }
            }

            $category->setIsStandard((int) $isStandard);

            $this->categoryRepository->save($category);
            $this->syncStoreValues($category, $data);
            $this->updateDescendantStandardState((int) $category->getId(), $isStandard);
            $this->messageManager->addSuccessMessage(__('Category saved successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Unable to save category: %1', $e->getMessage()));
        }

        return $resultRedirect->setPath('*/*/index');
    }

    /**
     * Convert a value to a boolean integer (1 or 0).
     *
     * @param mixed $value
     * @return int
     */
    private function toBoolean($value): int
    {
        if (is_string($value)) {
            return in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true) ? 1 : 0;
        }

        return !empty($value) ? 1 : 0;
    }

    /**
     * Normalize the description value to ensure it is a string
     *
     * @param mixed $description
     * @return string
     */
    private function normalizeDescription($description): string
    {
        if (is_array($description)) {
            $description = $description['value'] ?? $description['content'] ?? '';
        }

        return is_scalar($description) ? (string) $description : '';
    }

    /**
     * Synchronize the attribute values of the given category across all store views.
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param array $data
     * @return void
     */
    private function syncStoreValues($category, array $data): void
    {
        $values = $this->getAttributeValues($category, $data);

        $this->saveAttributesAtScope($category, $values);

        foreach ($this->storeManager->getStores() as $store) {
            $storeCategory = $this->categoryFactory->create()
                ->setStoreId((int) $store->getId())
                ->load((int) $category->getId());

            $this->saveAttributesAtScope($storeCategory, $values);
        }
    }

    /**
     * Get the attribute values for the given category based on the provided data.
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param array $data
     * @return array
     */
    private function getAttributeValues($category, array $data): array
    {
        $values = [];
        $ignored = ['entity_id', 'parent_id', 'form_key', 'key'];
        $resource = $category->getResource();

        foreach ($data as $attributeCode => $value) {
            if (in_array($attributeCode, $ignored, true)) {
                continue;
            }

            $attribute = $resource->getAttribute($attributeCode);
            if (!$attribute || !$attribute->getId()) {
                continue;
            }

            if ($attributeCode === 'description') {
                $value = $this->normalizeDescription($value);
            } elseif ($attributeCode === 'image' || $attributeCode === 'category_banner') {
                $value = $category->getData('image');
                if ($attributeCode === 'category_banner') {
                    $value = $category->getData('category_banner');
                }
            } elseif ($attributeCode === 'is_active' || $attributeCode === 'include_in_menu') {
                $value = $this->toBoolean($value);
            }

            $values[$attributeCode] = $value;
        }

        $values['name'] = $data['name'] ?? $category->getName();
        $values['is_active'] = $this->toBoolean($data['is_active'] ?? $category->getIsActive());
        $values['is_standard'] = (int) $category->getData('is_standard');

        return $values;
    }

    /**
     * Save the specified attribute values for the given category at its current scope.
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param array $values
     * @return void
     */
    private function saveAttributesAtScope($category, array $values): void
    {
        foreach ($values as $attribute => $value) {
            $category->setData($attribute, $value);
            $category->getResource()->saveAttribute($category, $attribute);
        }
    }

    /**
     * Recursively update the 'is_standard' attribute for all descendant categories.
     *
     * @param int $parentId
     * @param int $isStandard
     * @return void
     */
    private function updateDescendantStandardState(int $parentId, int $isStandard): void
    {
        $children = $this->categoryFactory->create()
            ->getCollection()
            ->addAttributeToFilter('parent_id', $parentId);

        foreach ($children as $child) {
            $child->setIsStandard($isStandard);
            $this->categoryRepository->save($child);
            $this->updateDescendantStandardState((int) $child->getId(), $isStandard);
        }
    }
}
