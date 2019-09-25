<?php

declare(strict_types=1);

namespace App\DataFixtures\ORM;

use App\Entity\Event;
use App\Entity\Location;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Sulu\Bundle\MediaBundle\Entity\Collection;
use Sulu\Bundle\MediaBundle\Entity\CollectionInterface;
use Sulu\Bundle\MediaBundle\Entity\CollectionMeta;
use Sulu\Bundle\MediaBundle\Entity\CollectionType;
use Sulu\Bundle\MediaBundle\Entity\File;
use Sulu\Bundle\MediaBundle\Entity\FileVersion;
use Sulu\Bundle\MediaBundle\Entity\FileVersionMeta;
use Sulu\Bundle\MediaBundle\Entity\Media;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;
use Sulu\Bundle\MediaBundle\Entity\MediaType;
use Sulu\Bundle\MediaBundle\Media\Storage\StorageInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AppFixtures extends Fixture implements OrderedFixtureInterface
{
    const LOCALE = 'en';

    /**
     * @var StorageInterface
     */
    private $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function getOrder()
    {
        return 10;
    }

    public function load(ObjectManager $manager): void
    {
        $collections = $this->loadCollections($manager);
        $images = $this->loadImages($manager, $collections['Content']);
        $this->loadEvents($manager, $images);

        $manager->flush();
    }

    /**
     * @param array<string, MediaInterface> $images
     */
    private function loadEvents(ObjectManager $manager, array $images): void
    {
        $eventRepository = $manager->getRepository(Event::class);
        $locationRepository = $manager->getRepository(Location::class);

        $data = [
            [
                'title' => 'SymfonyCon ??? 2020',
                'location' => null,
                'image' => null,
                'teaser' => '',
                'description' => '',
                'startDate' => '2020-11-21',
                'endDate' => '2020-11-23',
                'enabled' => false,
            ],
            [
                'title' => 'SymfonyCon Amsterdam 2019',
                'image' => 'amsterdam.jpg',
                'location' => 'Amsterdam',
                'teaser' => 'Symfony is proud to organize the seventh edition of the SymfonyCon, the international Symfony conference.',
                'description' => 'Symfony is proud to organize the seventh edition of the SymfonyCon, the international Symfony conference. This year, to celebrate Symfony, we decided to bring the entire community to the Netherlands and discover the amazing city of Amsterdam. If you like Symfony and share fun with professionals, this is where you want to be on November!',
                'startDate' => '2019-11-21',
                'endDate' => '2019-11-23',
                'enabled' => true,
            ],
            [
                'title' => 'SymfonyLive Berlin 2019',
                'image' => 'berlin.png',
                'location' => 'Berlin',
                'teaser' => 'Die SymfonyLive kommt wieder nach Berlin!',
                'description' => 'Die SymfonyLive kommt wieder nach Berlin! Nach dem Erfolg der Konferenz 2018 lassen wir es uns natürlich nicht nehmen, auch die nächste SymfonyLive in der Hauptstadt auszurichten.',
                'startDate' => '2019-09-24',
                'endDate' => '2019-09-27',
                'enabled' => true,
            ],
            [
                'title' => 'SymfonyLive London 2019',
                'image' => 'london.jpg',
                'location' => 'London',
                'teaser' => 'Join us from September 12th to 13th 2019 for SymfonyLive London 2019!',
                'description' => 'Join us from September 12th to 13th 2019 for SymfonyLive London 2019! We are proud to organize the 8th edition of the Symfony conference in London and to welcome the Symfony community from all over the UK.',
                'startDate' => '2019-09-12',
                'endDate' => '2019-09-13',
                'enabled' => true,
            ],
            [
                'title' => 'SymfonyLive Warszawa 2019',
                'image' => 'warszawa.jpg',
                'location' => 'Warszawa',
                'teaser' => 'Dołącz do nas w dniach 13-14 czerwca na dwa niesamowite dni z Symfony w Warszawie.',
                'description' => 'Dołącz do nas w dniach 13-14 czerwca na dwa niesamowite dni z Symfony w Warszawie. Pomimo anglojęzycznej strony, SymfonyLive Warsaw jest konferencją lokalną prowadzoną w języku polskim. Wyjątkiem są prezentacje przewodnie prowadzone w języku angielskim.',
                'startDate' => '2019-06-13',
                'endDate' => '2019-06-14',
                'enabled' => true,
            ],
            [
                'title' => 'SymfonyLive Sao Paulo 2019',
                'image' => 'sao-paulo.jpg',
                'location' => 'Sao Paulo',
                'teaser' => 'Bem-vindo ao SymfonyLive Brasil, a conferência oficial dedicada ao Symfony no Brasil.',
                'description' => 'Bem-vindo ao SymfonyLive Brasil, a conferência oficial dedicada ao Symfony no Brasil. Nosso objetivo é reunir a comunidade Symfony brasileira para conhecer todos os melhores e mais recentes desenvolvimentos com Symfony!',
                'startDate' => '2019-05-16',
                'endDate' => '2019-05-17',
                'enabled' => true,
            ],
            [
                'title' => 'SymfonyLive Tunis 2019',
                'image' => 'tunis.jpg',
                'location' => 'Tunis',
                'teaser' => 'Nous sommes ravis de vous donner rendez-vous le samedi 27 avril au Mövenpick Hotel du Lac à Tunis pour une journée complète de conférences sur Symfony.',
                'description' => 'Nous sommes ravis de vous donner rendez-vous le samedi 27 avril au Mövenpick Hotel du Lac à Tunis pour une journée complète de conférences sur Symfony. Nous organisons pour la première fois une conférence SymfonyLive à Tunis et nous avons hâte de retrouver la communauté locale de Symfony !',
                'startDate' => '2019-04-27',
                'endDate' => '2019-04-27',
                'enabled' => true,
            ],
        ];

        foreach ($data as $item) {
            $location = null;
            if ($item['location']) {
                $location = $locationRepository->create();
                $location->setName($item['location']);
                $location->setStreet('');
                $location->setNumber('');
                $location->setCity('');
                $location->setCountryCode('');
                $location->setPostalCode('');
                $locationRepository->save($location);
            }

            $event = $eventRepository->create(self::LOCALE);

            $event->setTitle($item['title']);
            $event->setImage($images[$item['image']] ?? null);
            $event->setLocation($location);
            $event->setTeaser($item['teaser']);
            $event->setDescription('<p>' . $item['description'] . '</p>');
            $event->setStartDate(new \DateTimeImmutable($item['startDate']));
            $event->setEndDate(new \DateTimeImmutable($item['endDate']));
            $event->setEnabled($item['enabled']);

            $eventRepository->save($event);
        }
    }

    /**
     * @return array<string, CollectionInterface>
     */
    private function loadCollections(ObjectManager $manager): array
    {
        $collections = [
            'Content' => $this->createCollection($manager, 'Content'),
        ];

        return $collections;
    }

    /**
     * @return MediaInterface[]
     */
    private function loadImages(ObjectManager $manager, CollectionInterface $collection): array
    {
        $finder = new Finder();
        $media = [];

        foreach ($finder->files()->in(__DIR__ . '/../images') as $file) {
            $media[$file->getBasename()] = $this->createMedia($manager, $file, $collection);
        }

        return $media;
    }

    private function createCollection(ObjectManager $manager, string $title): CollectionInterface
    {
        $collection = new Collection();
        /** @var CollectionType|null $collectionType */
        $collectionType = $manager->getRepository(CollectionType::class)->find(1);

        if (!$collectionType) {
            throw new \RuntimeException('CollectionType "1" not found. Maybe sulu fixtures missing?');
        }

        $collection->setType($collectionType);
        $meta = new CollectionMeta();
        $meta->setLocale(self::LOCALE);
        $meta->setTitle($title);
        $meta->setCollection($collection);

        $collection->addMeta($meta);
        $collection->setDefaultMeta($meta);

        $manager->persist($collection);
        $manager->persist($meta);

        return $collection;
    }

    private function createMedia(
        ObjectManager $manager,
        SplFileInfo $file,
        CollectionInterface $collection
    ): MediaInterface {
        $fileName = $file->getBasename();
        $title = $file->getFilename();
        $uploadedFile = new UploadedFile($file->getPathname(), $fileName);

        $storageOptions = $this->storage->save(
            $uploadedFile->getPathname(),
            $fileName
        );

        $mediaType = $manager->getRepository(MediaType::class)->find(2);

        if (!$mediaType instanceof MediaType) {
            throw new \RuntimeException('MediaType "2" not found. Maybe sulu fixtures missing?');
        }

        $media = new Media();

        $file = new File();
        $file->setVersion(1)
            ->setMedia($media);

        $media->addFile($file)
            ->setType($mediaType)
            ->setCollection($collection);

        $fileVersion = new FileVersion();
        $fileVersion->setVersion($file->getVersion())
            ->setSize($uploadedFile->getSize())
            ->setName($fileName)
            ->setStorageOptions($storageOptions)
            ->setMimeType($uploadedFile->getMimeType() ?: 'image/jpeg')
            ->setFile($file);

        $file->addFileVersion($fileVersion);

        $fileVersionMeta = new FileVersionMeta();
        $fileVersionMeta->setTitle($title)
            ->setDescription('')
            ->setLocale(self::LOCALE)
            ->setFileVersion($fileVersion);

        $fileVersion->addMeta($fileVersionMeta)
            ->setDefaultMeta($fileVersionMeta);

        $manager->persist($fileVersionMeta);
        $manager->persist($fileVersion);
        $manager->persist($media);

        return $media;
    }
}
