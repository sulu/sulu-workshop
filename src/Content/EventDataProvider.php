<?php

declare(strict_types=1);

namespace App\Content;

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
                        ['column' => 'event_translation.title', 'title' => 'sulu_admin.title'],
                    ]
                )
                ->getConfiguration();
        }

        return parent::getConfiguration();
    }

    protected function decorateDataItems(array $data)
    {
        return array_map(
            function ($item) {
                return new EventDataItem($item);
            },
            $data
        );
    }
}
