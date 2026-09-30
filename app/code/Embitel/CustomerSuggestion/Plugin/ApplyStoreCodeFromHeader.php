<?php
declare(strict_types=1);

namespace Embitel\CustomerSuggestion\Plugin;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;

class ApplyStoreCodeFromHeader
{
    private const HEADER_CANDIDATES = [
        'Store',
        'store',
        'Store-Code',
        'store-code',
        'X-Store-Code',
        'x-store-code',
    ];

    public function __construct(
        private readonly RequestInterface $request,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public function beforeSave($subject, $customerSuggestion): array
    {
        $this->applyStoreFromHeader();
        return [$customerSuggestion];
    }

    public function beforeGet($subject, $entityId): array
    {
        $this->applyStoreFromHeader();
        return [$entityId];
    }

    public function beforeGetList($subject, SearchCriteriaInterface $searchCriteria): array
    {
        $this->applyStoreFromHeader();
        return [$searchCriteria];
    }

    public function beforeDelete($subject, $customerSuggestion): array
    {
        $this->applyStoreFromHeader();
        return [$customerSuggestion];
    }

    public function beforeDeleteById($subject, $entityId): array
    {
        $this->applyStoreFromHeader();
        return [$entityId];
    }

    private function applyStoreFromHeader(): void
    {
        $storeCode = $this->resolveStoreCodeFromRequest();
        if ($storeCode === null || $storeCode === '') {
            return;
        }

        try {
            $this->storeManager->setCurrentStore($storeCode);
        } catch (\Exception $exception) {
            throw new LocalizedException(
                __('Invalid store code provided in request header: %1', $storeCode)
            );
        }
    }

    private function resolveStoreCodeFromRequest(): ?string
    {
        foreach (self::HEADER_CANDIDATES as $headerName) {
            if (method_exists($this->request, 'getHeader')) {
                $value = (string)$this->request->getHeader($headerName);
                if (trim($value) !== '') {
                    return trim($value);
                }
            }
        }

        foreach (['HTTP_STORE', 'HTTP_STORE_CODE', 'HTTP_X_STORE_CODE'] as $serverKey) {
            if (method_exists($this->request, 'getServer')) {
                $value = (string)$this->request->getServer($serverKey);
                if (trim($value) !== '') {
                    return trim($value);
                }
            }
        }

        return null;
    }
}
