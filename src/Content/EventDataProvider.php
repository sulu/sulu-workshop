<?php

declare(strict_types=1);

namespace App\Content;

use App\Entity\Event;
use Sulu\Component\SmartContent\Orm\BaseDataProvider;

class EventDataProvider extends BaseDataProvider
{
    public function getConfiguration()
    {
        if (null === $this->configuration) {
            $this->configuration = self::createConfigurationBuilder()
                ->enableLimit()
                ->enablePagination()
                ->enableSorting(
                    [
                        ['column' => 'translation.title', 'title' => 'sulu_admin.title'],
                    ],
                )
                ->getConfiguration();
        }

        return parent::getConfiguration();
    }

    /**
     * @param Event[] $data
     */
    protected function decorateDataItems(array $data): array
    {
        return \array_map(
            function (Event $item) {
                return new EventDataItem($item);
            },
            $data,
        );
    }
}
