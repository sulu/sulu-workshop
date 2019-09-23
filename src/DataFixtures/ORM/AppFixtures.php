<?php

declare(strict_types=1);

namespace App\DataFixtures\ORM;

use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    const LOCALE = 'en';

    public function load(ObjectManager $manager)
    {
        $repository = $manager->getRepository(Event::class);

        $data = [
            [
                'title' => 'SymfonyCon ??? 2020',
                'location' => null,
                'teaser' => '',
                'description' => '',
                'startDate' => '2020-11-21',
                'endDate' => '2020-11-23',
                'enabled' => false,
            ],
            [
                'title' => 'SymfonyCon Amsterdam 2019',
                'location' => 'Amsterdam',
                'teaser' => 'Symfony is proud to organize the seventh edition of the SymfonyCon, the international Symfony conference.',
                'description' => 'Symfony is proud to organize the seventh edition of the SymfonyCon, the international Symfony conference. This year, to celebrate Symfony, we decided to bring the entire community to the Netherlands and discover the amazing city of Amsterdam. If you like Symfony and share fun with professionals, this is where you want to be on November!',
                'startDate' => '2019-11-21',
                'endDate' => '2019-11-23',
                'enabled' => true,
            ],
            [
                'title' => 'SymfonyLive Berlin 2019',
                'location' => 'Berlin',
                'teaser' => 'Die SymfonyLive kommt wieder nach Berlin!',
                'description' => 'Die SymfonyLive kommt wieder nach Berlin! Nach dem Erfolg der Konferenz 2018 lassen wir es uns natürlich nicht nehmen, auch die nächste SymfonyLive in der Hauptstadt auszurichten.',
                'startDate' => '2019-09-24',
                'endDate' => '2019-09-27',
                'enabled' => true,
            ],
            [
                'title' => 'SymfonyLive London 2019',
                'location' => 'London',
                'teaser' => 'Join us from September 12th to 13th 2019 for SymfonyLive London 2019!',
                'description' => 'Join us from September 12th to 13th 2019 for SymfonyLive London 2019! We are proud to organize the 8th edition of the Symfony conference in London and to welcome the Symfony community from all over the UK.',
                'startDate' => '2019-09-12',
                'endDate' => '2019-09-13',
                'enabled' => true,
            ],
            [
                'title' => 'SymfonyLive Warszawa 2019',
                'location' => 'Warszawa',
                'teaser' => 'Dołącz do nas w dniach 13-14 czerwca na dwa niesamowite dni z Symfony w Warszawie.',
                'description' => 'Dołącz do nas w dniach 13-14 czerwca na dwa niesamowite dni z Symfony w Warszawie. Pomimo anglojęzycznej strony, SymfonyLive Warsaw jest konferencją lokalną prowadzoną w języku polskim. Wyjątkiem są prezentacje przewodnie prowadzone w języku angielskim.',
                'startDate' => '2019-06-13',
                'endDate' => '2019-06-14',
                'enabled' => true,
            ],
            [
                'title' => 'SymfonyLive Sao Paulo 2019',
                'location' => 'Sao Paulo',
                'teaser' => 'Bem-vindo ao SymfonyLive Brasil, a conferência oficial dedicada ao Symfony no Brasil.',
                'description' => 'Bem-vindo ao SymfonyLive Brasil, a conferência oficial dedicada ao Symfony no Brasil. Nosso objetivo é reunir a comunidade Symfony brasileira para conhecer todos os melhores e mais recentes desenvolvimentos com Symfony!',
                'startDate' => '2019-05-16',
                'endDate' => '2019-05-17',
                'enabled' => true,
            ],
            [
                'title' => 'SymfonyLive Tunis 2019',
                'location' => 'Tunis',
                'teaser' => 'Nous sommes ravis de vous donner rendez-vous le samedi 27 avril au Mövenpick Hotel du Lac à Tunis pour une journée complète de conférences sur Symfony.',
                'description' => 'Nous sommes ravis de vous donner rendez-vous le samedi 27 avril au Mövenpick Hotel du Lac à Tunis pour une journée complète de conférences sur Symfony. Nous organisons pour la première fois une conférence SymfonyLive à Tunis et nous avons hâte de retrouver la communauté locale de Symfony !',
                'startDate' => '2019-04-27',
                'endDate' => '2019-04-27',
                'enabled' => true,
            ],
        ];

        foreach ($data as $item) {
            $event = $repository->create(self::LOCALE);

            $event->setTitle($item['title']);
            $event->setLocation($item['location']);
            $event->setTeaser($item['teaser']);
            $event->setDescription('<p>' . $item['description'] . '</p>');
            $event->setStartDate(new \DateTimeImmutable($item['startDate']));
            $event->setEndDate(new \DateTimeImmutable($item['endDate']));
            $event->setEnabled($item['enabled']);

            $repository->save($event);
        }

        $manager->flush();
    }
}
