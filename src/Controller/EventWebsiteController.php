<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\EventRepository;
use Sulu\Bundle\WebsiteBundle\Resolver\TemplateAttributeResolverInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EventWebsiteController extends AbstractController
{
    public function indexAction(int $id, Request $request): Response
    {
        $event = $this->get(EventRepository::class)->findById($id, $request->getLocale());
        if (!$event) {
            throw new NotFoundHttpException();
        }

        return $this->render(
            'events/index.html.twig',
            $this->get(TemplateAttributeResolverInterface::class)->resolve(
                [
                    'event' => $event,
                    'content' => ['title' => $event->getTitle()],
                ]
            )
        );
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                EventRepository::class,
                TemplateAttributeResolverInterface::class,
            ]
        );
    }
}
