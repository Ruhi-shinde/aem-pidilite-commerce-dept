<?php
/**
 * Copyright © Embitel, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Embitel\Catalog\Plugin;

use Magento\DownloadableGraphQl\Model\ConvertSamplesToArray;
use Magento\Downloadable\Model\SampleFactory;
use Magento\Framework\UrlInterface;

/**
 * Plugin class to add custom fields to downloadable product samples.
 */
class GetDownloadableSamples
{
    /**
     * Intialize dependencies.
     *
      * @param SampleFactory $sampleFactory
     */
    public function __construct(
          private SampleFactory $sampleFactory
    ) {
    }

    /**
     * After plugin for ConvertSamplesToArray::execute method.
     *
     * @param ConvertSamplesToArray $subject
     * @param array $result
     * @return array
     */
    public function afterExecute(
        ConvertSamplesToArray $subject,
        array $result
    ): array {
        foreach ($result as &$sample) {
            if (empty($sample['id'])) {
                continue;
            }

            $sampleId = (int) $sample['id'];

            $sampleModel = $this->sampleFactory->create();
            $sampleModel->load($sampleId);

            $sample['updated_at'] = $sampleModel->getData('updated_at');
            $sampleFile = (string) $sampleModel->getData('sample_file');
            $sample['sample_file'] = $sampleFile ?: null;            
        }

        return $result;
    }
}