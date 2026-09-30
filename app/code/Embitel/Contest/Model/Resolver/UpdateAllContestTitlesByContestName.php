<?php
declare(strict_types=1);

namespace Embitel\Contest\Model\Resolver;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class UpdateAllContestTitlesByContestName implements ResolverInterface
{
    private ResourceConnection $resourceConnection;

    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        try {
            $connection = $this->resourceConnection->getConnection();
            $tableName = $this->resourceConnection->getTableName('embitel_contest_contest');

            $contestUpdates = $args['contest_updates'] ?? [];

            if (!is_array($contestUpdates) || $contestUpdates === []) {
                throw new GraphQlInputException(
                    __('contest_updates is required and cannot be empty.')
                );
            }

            $updatedCount = 0;

            foreach ($contestUpdates as $index => $contestUpdate) {
                if (!is_array($contestUpdate)) {
                    throw new GraphQlInputException(
                        __('Invalid input at position %1.', $index)
                    );
                }

                $contestName = trim((string)($contestUpdate['contest_name'] ?? ''));
                $contestTitle = trim((string)($contestUpdate['contest_title'] ?? ''));

                if ($contestName === '') {
                    throw new GraphQlInputException(
                        __('Contest name is required at position %1.', $index)
                    );
                }

                if ($contestTitle === '') {
                    throw new GraphQlInputException(
                        __('Contest title is required at position %1.', $index)
                    );
                }

                $where = [
                    'contest_name = ?' => $contestName,
                    '(contest_title IS NULL OR contest_title <> ?)' => $contestTitle
                ];

                $affectedRows = (int)$connection->update(
                    $tableName,
                    [
                        'contest_title' => $contestTitle
                    ],
                    $where
                );

                if ($affectedRows > 0) {
                    $updatedCount++;
                }
            }

            $message = $updatedCount > 0
                ? __('Updated titles for %1 contest name(s).', $updatedCount)
                : __('No contest titles needed updating.');

            return [
                'success' => true,
                'message' => $message->render(),
                'updated_count' => $updatedCount
            ];
        } catch (LocalizedException $exception) {
            throw new GraphQlInputException(__($exception->getMessage()));
        } catch (\Throwable $exception) {
            throw new GraphQlInputException(
                __('Unable to update contest titles right now.')
            );
        }
    }
}