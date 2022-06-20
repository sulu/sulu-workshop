<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Repository\EventRepository;
use Sulu\Bundle\WebsiteBundle\Resolver\TemplateAttributeResolverInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EventWebsiteController extends AbstractController
{
    /**
     * @var EventRepository
     */
    private $eventRepository;

    /**
     * @var TemplateAttributeResolverInterface
     */
    private $templateAttributeResolver;

    public function __construct(
        EventRepository $repository,
        TemplateAttributeResolverInterface $templateAttributeResolver
    ) {
        $this->eventRepository = $repository;
        $this->templateAttributeResolver = $templateAttributeResolver;
    }

    public function indexAction(int $id, Request $request): Response
    {
        $event = $this->eventRepository->findById($id, $request->getLocale());
        if (!$event) {
            throw new NotFoundHttpException();
        }

        return $this->render(
            'events/index.html.twig',
            $this->templateAttributeResolver->resolve(
                [
                    'event' => $event,
                    'content' => ['title' => $event->getTitle()],
                ]
            )
        );
    }
}
