<?php
declare(strict_types=1);

namespace Embitel\Contest\Model\Resolver;

use Embitel\Contest\Model\ResourceModel\Contest\CollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\UrlInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Store\Model\StoreManagerInterface;

class Contests implements ResolverInterface
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
        $mediaBaseUrl = rtrim(
            $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA),
            '/'
        );

        $result = [];

        foreach ($collection as $contest) {
            $documents = $contest->getData('documents');

            if (!empty($documents)) {
                $decodedDocuments = json_decode(
                    (string)$documents,
                    true
                );

                if (is_array($decodedDocuments)) {
                    $documents = $decodedDocuments;
                } else {
                    // Support old records where documents is plain text
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

            $result[] = [
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

        return $result;
    }
}