<?php
declare(strict_types=1);

namespace Embitel\Idealab\Model\Resolver;

use Embitel\Idealab\Api\IdealabRepositoryInterface;
use Embitel\Idealab\Model\IdealabFactory;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class CreateIdealab implements ResolverInterface
{
    private IdealabFactory $idealabFactory;
    private IdealabRepositoryInterface $idealabRepository;
    private Filesystem $filesystem;
    private StoreManagerInterface $storeManager;

    public function __construct(
        IdealabFactory $idealabFactory,
        IdealabRepositoryInterface $idealabRepository,
        Filesystem $filesystem,
        ?StoreManagerInterface $storeManager = null
    ) {
        $this->idealabFactory = $idealabFactory;
        $this->idealabRepository = $idealabRepository;
        $this->filesystem = $filesystem;
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
        $input = $args['input'] ?? [];
        $customerId = $this->getAuthenticatedCustomerId($context);

        $craftImageInput = $input['craft_image'] ?? [];
        if (!is_array($craftImageInput)) {
            throw new LocalizedException(__('craft_image must be an array.'));
        }

        if (count($craftImageInput) > 3) {
            throw new LocalizedException(__('craft_image can contain a maximum of 3 images.'));
        }

        $uploadedImages = [];
        foreach ($craftImageInput as $imageBase64) {
            if (!empty($imageBase64)) {
                $uploadedImages[] = $this->saveBase64Image($imageBase64);
            }
        }

        $idealab = $this->idealabFactory->create();
        $idealab->setData([
            'user_id' => $customerId,
            'student_first_name' => $input['student_first_name'] ?? '',
            'student_last_name' => $input['student_last_name'] ?? '',
            'student_date_of_birth' => $input['student_date_of_birth'] ?? '',
            'student_school_name' => $input['student_school_name'] ?? '',
            'gender' => $input['gender'] ?? '',
            'grade_group' => $input['grade_group'] ?? '',
            'parent_email' => $input['parent_email'] ?? '',
            'parent_phone' => $input['parent_phone'] ?? '',
            'project_title' => $input['project_title'] ?? '',
            'video_url' => $input['video_url'] ?? null,
            'craft_image' => json_encode($uploadedImages, JSON_UNESCAPED_SLASHES),
            'project_description' => $input['project_description'] ?? '',
            'i_accept' => (int)(bool)($input['i_accept'] ?? false)
        ]);

        $this->idealabRepository->save($idealab);

        return [
            'success' => true,
            'message' => __('Idea Lab submission created successfully.')->render(),
            'idealab' => $this->mapIdealabData($idealab->getData())
        ];
    }

    private function getAuthenticatedCustomerId($context): int
    {
        $customerId = (int)$context->getUserId();
        $userType = (int)$context->getUserType();

        if ($customerId <= 0 || $userType !== UserContextInterface::USER_TYPE_CUSTOMER) {
            throw new GraphQlAuthorizationException(
                __('Customer token is missing or invalid.')
            );
        }

        return $customerId;
    }

    private function saveBase64Image(string $base64Image): string
    {
        if (!preg_match('/^data:(image\\/jpeg|image\\/jpg|image\\/png|image\\/gif);base64,(.+)$/', $base64Image, $matches)) {
            throw new LocalizedException(__('Invalid image format. Supported images are JPG, PNG and GIF.'));
        }

        $mimeType = $matches[1];
        $base64Data = $matches[2];

        $extensionMap = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif'
        ];

        $extension = $extensionMap[$mimeType] ?? null;
        if (!$extension) {
            throw new LocalizedException(__('Unsupported image type.'));
        }

        $fileData = base64_decode($base64Data, true);
        if ($fileData === false) {
            throw new LocalizedException(__('Unable to decode uploaded image.'));
        }

        if (strlen($fileData) > 5 * 1024 * 1024) {
            throw new LocalizedException(__('Each image must be less than 5 MB.'));
        }

        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $directory = 'idealab';

        if (!$mediaDirectory->isExist($directory)) {
            $mediaDirectory->create($directory);
        }

        $fileName = uniqid('idealab_', true) . '.' . $extension;
        $filePath = $directory . '/' . $fileName;

        $mediaDirectory->writeFile($filePath, $fileData);

        return $filePath;
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
