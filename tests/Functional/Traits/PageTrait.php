<?php

declare(strict_types=1);

namespace App\Tests\Functional\Traits;

use Sulu\Bundle\PageBundle\Document\PageDocument;
use Sulu\Component\Content\Document\WorkflowStage;
use Sulu\Component\DocumentManager\DocumentManagerInterface;

trait PageTrait
{
    /**
     * @param mixed[] $data
     */
    protected function createPage(
        string $template,
        string $webspaceKey,
        array $data,
        string $locale = 'en'
    ): PageDocument {
        $documentManager = static::getDocumentManager();

        /** @var PageDocument $document */
        $document = $documentManager->create('page');

        if (!$document instanceof PageDocument) {
            throw new \RuntimeException('Invalid document');
        }

        $document->setLocale($locale);
        $document->setTitle($data['title']);
        $document->setStructureType($template);
        $document->setResourceSegment($data['url']);

        if ($data['published']) {
            $document->setWorkflowStage(WorkflowStage::PUBLISHED);
        }

        $document->getStructure()->bind($data);

        $documentManager->persist($document, $locale, ['parent_path' => '/cmf/' . $webspaceKey . '/contents']);

        if ($data['published']) {
            $documentManager->publish($document, $locale);
        }

        $documentManager->flush();

        return $document;
    }

    abstract protected static function getDocumentManager(): DocumentManagerInterface;
}
