<?php
declare(strict_types=1);

namespace Embitel\Contest\Model\Resolver;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\UrlInterface;
use Embitel\Contest\Model\ContestFactory;
use Embitel\Contest\Api\ContestRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class CreateContest implements ResolverInterface
{
    private ContestFactory $contestFactory;
    private ContestRepositoryInterface $contestRepository;
    private Filesystem $filesystem;
    private File $fileIo;
    private StoreManagerInterface $storeManager;

    public function __construct(
        ContestFactory $contestFactory,
        ContestRepositoryInterface $contestRepository,
        Filesystem $filesystem,
        File $fileIo,
        ?StoreManagerInterface $storeManager = null
    ) {
        $this->contestFactory = $contestFactory;
        $this->contestRepository = $contestRepository;
        $this->filesystem = $filesystem;
        $this->fileIo = $fileIo;
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

        $contest = $this->contestFactory->create();

        $contest->setUserId($customerId);
        $contest->setChildName($input['child_name'] ?? '');

        $contestName = trim((string)($input['contest_name'] ?? ''));
        $contestTitleInput = $input['contest_title'] ?? '';
        $contest->setContestName($contestName);
        $contest->setContestTitle($contestTitleInput);
        $contest->setBannerImage($input['banner_image'] ?? '');
        $contest->setCtaLink($input['cta_link'] ?? '');
        $contest->setParentEmail($input['parent_email'] ?? '');
        $contest->setParentPhone($input['parent_phone'] ?? '');
        $contest->setArtworkTitle($input['artwork_title'] ?? '');
        $contest->setDescription($input['description'] ?? '');

        /*
         * Handle up to 3 uploaded documents.
         *
         * Expected input:
         *
         * documents: [
         *     "data:image/png;base64,iVBORw0KGgo...",
         *     "data:application/pdf;base64,JVBERi0x...",
         *     "data:image/jpeg;base64,/9j/4AAQ..."
         * ]
         */
        $documents = $input['documents'] ?? [];

        if (!is_array($documents)) {
            throw new LocalizedException(
                __('Documents must be an array.')
            );
        }

        if (count($documents) > 3) {
            throw new LocalizedException(
                __('You can upload a maximum of 3 files.')
            );
        }

        $uploadedFiles = [];

foreach ($documents as $document) {
    if (!empty($document)) {
        $uploadedFiles[] = $this->saveBase64File($document);
    }
}

$contest->setDocuments(
    json_encode($uploadedFiles, JSON_UNESCAPED_SLASHES)
);

$this->contestRepository->save($contest);

$mediaBaseUrl = rtrim(
    $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA),
    '/'
);

$documentUrls = array_values(array_filter(array_map(
    static function ($document) use ($mediaBaseUrl) {
        $documentPath = trim((string)$document);

        if ($documentPath === '') {
            return null;
        }

        if (preg_match('#^https?://#i', $documentPath)) {
            return $documentPath;
        }

        return $mediaBaseUrl . '/' . ltrim($documentPath, '/');
    },
    $uploadedFiles
)));

        return [
    'success' => true,
    'message' => __('Contest created successfully.')->render(),
    'contest' => [
        'contest_id' => (string)$contest->getContestId(),
        'contest_name' => $contest->getContestName(),
        'contest_title' => $contest->getContestTitle(),
        'banner_image' => $contest->getBannerImage(),
        'cta_link' => $contest->getCtaLink(),
        'user_id' => (string)$contest->getUserId(),
        'child_name' => $contest->getChildName(),
        'parent_email' => $contest->getParentEmail(),
        'parent_phone' => $contest->getParentPhone(),
        'artwork_title' => $contest->getArtworkTitle(),
        'description' => $contest->getDescription(),
        'documents' => $documentUrls
    ]
];
    }

    /**
     * Save base64 encoded file to pub/media/contest
     *
     * @param string $base64File
     * @return string
     * @throws LocalizedException
     */
    private function saveBase64File(string $base64File): string
    {
        /*
         * Extract MIME type and base64 content.
         *
         * Example:
         * data:image/png;base64,XXXXX
         */
        if (preg_match(
            '/^data:(image\/jpeg|image\/jpg|image\/png|image\/gif|application\/pdf);base64,(.+)$/',
            $base64File,
            $matches
        )) {
            $mimeType = $matches[1];
            $base64Data = $matches[2];
        } else {
            throw new LocalizedException(
                __('Invalid file format. Supported files are JPG, PNG, GIF and PDF.')
            );
        }

        $extensionMap = [
            'image/jpeg' => 'jpg',
            'image/jpg'  => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'application/pdf' => 'pdf'
        ];

        $extension = $extensionMap[$mimeType] ?? null;

        if (!$extension) {
            throw new LocalizedException(
                __('Unsupported file type.')
            );
        }

        $fileData = base64_decode($base64Data, true);

        if ($fileData === false) {
            throw new LocalizedException(
                __('Unable to decode uploaded file.')
            );
        }

        /*
         * Maximum file size: 5 MB per file.
         */
        if (strlen($fileData) > 5 * 1024 * 1024) {
            throw new LocalizedException(
                __('Each file must be less than 5 MB.')
            );
        }

        $mediaDirectory = $this->filesystem
            ->getDirectoryWrite(DirectoryList::MEDIA);

        $directory = 'contest';

        if (!$mediaDirectory->isExist($directory)) {
            $mediaDirectory->create($directory);
        }

        /*
         * Generate unique filename.
         */
        $fileName = uniqid('contest_', true) . '.' . $extension;

        $filePath = $directory . '/' . $fileName;

        $mediaDirectory->writeFile(
            $filePath,
            $fileData
        );

        return $filePath;
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
}