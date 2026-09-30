<?php
declare(strict_types=1);

namespace Embitel\Contest\Model\Resolver;

use Embitel\Contest\Model\ResourceModel\Contest\CollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class Contest implements ResolverInterface
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
        if (empty($args['contest_id'])) {
            throw new GraphQlInputException(
                __('contest_id is required.')
            );
        }

        $contestId = (int)$args['contest_id'];

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('contest_id', $contestId);
        $collection->setPageSize(1);

        $contest = $collection->getFirstItem();

        if (!$contest->getId()) {
            return null;
        }

        $mediaBaseUrl = rtrim(
            $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA),
            '/'
        );

        $documents = $contest->getData('documents');

        if (!empty($documents)) {
            $decodedDocuments = json_decode(
                (string)$documents,
                true
            );

            if (is_array($decodedDocuments)) {
                $documents = $decodedDocuments;
            } else {
                // If old records contain a plain string
                $documents = [$documents];
            }
        } else {
            $documents = [];
        }

        $documents = array_values(array_filter(array_map(
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
            $documents
        )));

        return [
            'contest_id' => (string)$contest->getData('contest_id'),
            'contest_name' => (string)$contest->getData('contest_name'),
            'contest_title' => (string)$contest->getData('contest_title'),
            'banner_image' => (string)$contest->getData('banner_image'),
            'cta_link' => (string)$contest->getData('cta_link'),
            'user_id' => (string)$contest->getData('user_id'),
            'child_name' => (string)$contest->getData('child_name'),
            'parent_email' => (string)$contest->getData('parent_email'),
            'parent_phone' => (string)$contest->getData('parent_phone'),
            'artwork_title' => (string)$contest->getData('artwork_title'),
            'description' => (string)$contest->getData('description'),
            'documents' => $documents
        ];
    }
}