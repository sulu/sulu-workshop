<?php

declare(strict_types=1);

namespace App\DataFixtures\ORM;

use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    const LOCALE = 'en';

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create(self::LOCALE);

        $repository = $manager->getRepository(Event::class);

        for ($i = 0; $i <= 20; ++$i) {
            $event = $repository->create(self::LOCALE);

            $startDate = \DateTimeImmutable::createFromMutable($faker->dateTime('+1 year'));
            $endDate = \DateTimeImmutable::createFromMutable(
                $faker->dateTimeInInterval($startDate->format('Y-m-d h:i:s'), '+4 hours')
            );

            $title = $faker->sentence(5);
            $event->setTitle(substr($title, 0, \strlen($title) - 1));
            $event->setDescription('<p>' . implode('</p><p>', (array) $faker->sentences()) . '</p>');
            $event->setStartDate($startDate);
            $event->setEndDate($endDate);

            $event->setEnabled($faker->boolean());
        }

        $manager->flush();
    }
}
