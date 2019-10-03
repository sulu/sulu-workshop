<?php

declare(strict_types=1);

namespace App\DataFixtures\Document;

use App\DataFixtures\ORM\AppFixtures;
use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\DocumentManagerBundle\DataFixtures\DocumentFixtureInterface;
use Sulu\Bundle\PageBundle\Document\HomeDocument;
use Sulu\Bundle\PageBundle\Document\PageDocument;
use Sulu\Component\Content\Document\RedirectType;
use Sulu\Component\Content\Document\WorkflowStage;
use Sulu\Component\DocumentManager\DocumentManager;
use Sulu\Component\PHPCR\PathCleanupInterface;

class DocumentFixture implements DocumentFixtureInterface
{
    /**
     * @var PathCleanupInterface
     */
    private $pathCleanup;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(PathCleanupInterface $pathCleanup, EntityManagerInterface $entityManager)
    {
        $this->pathCleanup = $pathCleanup;
        $this->entityManager = $entityManager;
    }

    public function getOrder()
    {
        return 10;
    }

    public function load(DocumentManager $documentManager): void
    {
        $this->loadPages($documentManager);
        $this->loadHomepage($documentManager);

        $documentManager->flush();
    }

    private function loadPages(DocumentManager $documentManager): void
    {
        $pageDataList = [
            [
                'title' => 'Imprint',
                'navigationContexts' => ['main'],
                'structureType' => 'default',
                'article' => '<p>This is a very good imprint :)</p>',
            ],
            [
                'title' => 'Events',
                'navigationContexts' => ['main'],
                'structureType' => 'event_overview',
                'article' => '',
            ],
        ];

        $pages = [];

        foreach ($pageDataList as $pageData) {
            $pages[$pageData['title']] = $this->createPage($documentManager, $pageData);
        }
    }

    private function loadHomepage(DocumentManager $documentManager): void
    {
        $repository = $this->entityManager->getRepository(Event::class);
        $events = $repository->findBy(['enabled' => true]);

        /** @var HomeDocument $homeDocument */
        $homeDocument = $documentManager->find('/cmf/example/contents', AppFixtures::LOCALE);

        $homeDocument->setTitle('Symfony official conferences');
        $homeDocument->getStructure()->bind(
            [
                'title' => $homeDocument->getTitle(),
                'url' => '/',
                'article' => '<p>Symfony official conference events.</p>',
                'events' => $this->getRandomEventIds($events, 3),
            ]
        );

        $documentManager->persist($homeDocument, AppFixtures::LOCALE);
        $documentManager->publish($homeDocument, AppFixtures::LOCALE);
    }

    /**
     * @param Event[] $events
     *
     * @return int[]
     */
    private function getRandomEventIds(array $events, int $count): array
    {
        $eventIds = [];
        while (\count($eventIds) < $count) {
            /** @var int $id */
            $id = $events[rand(0, \count($events) - 1)]->getId();
            $eventIds[$id] = $id;
        }

        return array_values($eventIds);
    }

    /**
     * @param mixed[] $data
     */
    private function createPage(DocumentManager $documentManager, array $data): PageDocument
    {
        if (!isset($data['url'])) {
            $url = $this->pathCleanup->cleanup('/' . $data['title']);
            if (isset($data['parent_path'])) {
                $url = mb_substr($data['parent_path'], mb_strlen('/cmf/example/contents')) . $url;
            }

            $data['url'] = $url;
        }

        $extensionData = [
            'seo' => $data['seo'] ?? [],
            'excerpt' => $data['excerpt'] ?? [],
        ];

        unset($data['excerpt']);
        unset($data['seo']);

        /** @var PageDocument $pageDocument */
        $pageDocument = $documentManager->create('page');
        $pageDocument->setNavigationContexts($data['navigationContexts'] ?? []);
        $pageDocument->setLocale(AppFixtures::LOCALE);
        $pageDocument->setTitle($data['title']);
        $pageDocument->setResourceSegment($data['url']);
        $pageDocument->setStructureType($data['structureType'] ?? 'default');
        $pageDocument->setWorkflowStage(WorkflowStage::PUBLISHED);
        $pageDocument->getStructure()->bind($data);
        $pageDocument->setAuthor(1);
        $pageDocument->setExtensionsData($extensionData);

        if (isset($data['redirect'])) {
            $pageDocument->setRedirectType(RedirectType::EXTERNAL);
            $pageDocument->setRedirectExternal($data['redirect']);
        }

        $documentManager->persist(
            $pageDocument,
            AppFixtures::LOCALE,
            ['parent_path' => $data['parent_path'] ?? '/cmf/example/contents']
        );

        // Set dataSource to current page after persist as uuid is before not available
        if (isset($data['pages']['dataSource']) && '__CURRENT__' === $data['pages']['dataSource']) {
            $pageDocument->getStructure()->bind(
                [
                    'pages' => array_merge(
                        $data['pages'],
                        [
                            'dataSource' => $pageDocument->getUuid(),
                        ]
                    ),
                ]
            );

            $documentManager->persist(
                $pageDocument,
                AppFixtures::LOCALE,
                ['parent_path' => $data['parent_path'] ?? '/cmf/example/contents']
            );
        }

        $documentManager->publish($pageDocument, AppFixtures::LOCALE);

        return $pageDocument;
    }
}
