<?php
declare(strict_types=1);

namespace Embitel\Idealab\Model\Resolver;

use Embitel\Idealab\Model\ResourceModel\Idealab\CollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class Idealabs implements ResolverInterface
{
    private CollectionFactory $collectionFactory;
    private StoreManagerInterface $storeManager;

    public function __construct(
        CollectionFactory $collectionFactory,
        ?StoreManagerInterface $storeManager = null
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager
            ?? ObjectManager::getInstance()->get(StoreManagerInterface::class);
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        $collection = $this->collectionFactory->create();
        $collection->setOrder('created_at', 'DESC');
        $result = [];

        foreach ($collection as $idealab) {
            $result[] = $this->mapIdealabData($idealab->getData());
        }

        return $result;
    }

    private function mapIdealabData(array $data): array
    {
        $mediaBaseUrl = rtrim(
            $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA),
            '/'
        );

        $craftImage = $data['craft_image'] ?? null;
        $craftImageList = [];
        if (!empty($craftImage)) {
            $decoded = json_decode((string)$craftImage, true);
            if (is_array($decoded)) {
                $craftImageList = $decoded;
            }
        }

        $craftImageList = array_values(array_filter(array_map(
            static function ($image) use ($mediaBaseUrl) {
                $imagePath = trim((string)$image);

                if ($imagePath === '') {
                    return null;
                }

                if (preg_match('#^https?://#i', $imagePath)) {
                    return $imagePath;
                }

                return $mediaBaseUrl . '/' . ltrim($imagePath, '/');
            },
            $craftImageList
        )));

        return [
            'idealab_id' => isset($data['idealab_id']) ? (string)$data['idealab_id'] : null,
            'user_id' => isset($data['user_id']) ? (string)$data['user_id'] : null,
            'student_first_name' => isset($data['student_first_name']) ? (string)$data['student_first_name'] : null,
            'student_last_name' => isset($data['student_last_name']) ? (string)$data['student_last_name'] : null,
            'student_date_of_birth' => isset($data['student_date_of_birth']) ? (string)$data['student_date_of_birth'] : null,
            'student_school_name' => isset($data['student_school_name']) ? (string)$data['student_school_name'] : null,
            'gender' => isset($data['gender']) ? (string)$data['gender'] : null,
            'grade_group' => isset($data['grade_group']) ? (string)$data['grade_group'] : null,
            'parent_email' => isset($data['parent_email']) ? (string)$data['parent_email'] : null,
            'parent_phone' => isset($data['parent_phone']) ? (string)$data['parent_phone'] : null,
            'project_title' => isset($data['project_title']) ? (string)$data['project_title'] : null,
            'video_url' => isset($data['video_url']) ? (string)$data['video_url'] : null,
            'craft_image' => $craftImageList,
            'project_description' => isset($data['project_description']) ? (string)$data['project_description'] : null,
            'i_accept' => isset($data['i_accept']) ? (bool)$data['i_accept'] : false,
            'created_at' => isset($data['created_at']) ? (string)$data['created_at'] : null,
            'updated_at' => isset($data['updated_at']) ? (string)$data['updated_at'] : null
        ];
    }
}
