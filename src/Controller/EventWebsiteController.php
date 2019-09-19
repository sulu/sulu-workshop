<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\EventRepository;
use Sulu\Bundle\WebsiteBundle\Resolver\TemplateAttributeResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

class EventWebsiteController
{
    /**
     * @var EventRepository
     */
    private $eventRepository;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var TemplateAttributeResolverInterface
     */
    private $attributesResolver;

    public function __construct(
        EventRepository $eventRepository,
        Environment $twig,
        TemplateAttributeResolverInterface $attributesResolver
    ) {
        $this->eventRepository = $eventRepository;
        $this->twig = $twig;
        $this->attributesResolver = $attributesResolver;
    }

    public function indexAction(int $id, Request $request): Response
    {
        $event = $this->eventRepository->findById($id, $request->getLocale());
        if (!$event) {
            throw new NotFoundHttpException();
        }

        $content = $this->twig->render(
            'events/index.html.twig',
            $this->attributesResolver->resolve(['event' => $event, 'content' => ['title' => $event->getTitle()]])
        );

        return new Response($content);
    }
}
