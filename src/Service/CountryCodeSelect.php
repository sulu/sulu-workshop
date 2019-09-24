<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Intl\Countries;

class CountryCodeSelect
{
    /**
     * @return array[]
     */
    public function getValues(): array
    {
        $values = [];

        foreach (Countries::getNames() as $code => $title) {
            $values[] = [
                'name' => $code,
                'title' => $title,
            ];
        }

        return $values;
    }
}
