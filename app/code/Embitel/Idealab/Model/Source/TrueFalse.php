<?php
declare(strict_types=1);

namespace Embitel\Idealab\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class TrueFalse implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 1, 'label' => __('true')],
            ['value' => 0, 'label' => __('false')]
        ];
    }
}
