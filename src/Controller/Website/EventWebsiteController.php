<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Entity\Event;
use App\Form\EventRegistrationType;
use App\Repository\EventRegistrationRepository;
use App\Repository\EventRepository;
use Sulu\Bundle\WebsiteBundle\Resolver\TemplateAttributeResolverInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class EventWebsiteController extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly TemplateAttributeResolverInterface $templateAttributeResolver,
        private readonly EventRegistrationRepository $eventRegistrationRepository,
    ) {
    }

    #[Route('/{_locale}/event/{id}', name: 'app.event')]
    public function indexAction(int $id, Request $request): Response
    {
        $event = $this->eventRepository->findById($id, $request->getLocale());
        if (!$event instanceof Event) {
            throw new NotFoundHttpException();
        }

        $registration = $this->eventRegistrationRepository->create($event);
        $form = $this->createForm(EventRegistrationType::class, $registration);
        $form->add(
            'submit',
            SubmitType::class,
            [
                'label' => 'Create',
            ],
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->eventRegistrationRepository->save($registration);

            return $this->redirectToRoute(
                'app.event',
                [
                    'id' => $event->getId(),
                    'success' => true,
                ],
            );
        }

        return $this->render(
            'events/index.html.twig',
            $this->templateAttributeResolver->resolve(
                [
                    'event' => $event,
                    'success' => $request->query->get('success'),
                    'form' => $form->createView(),
                    'content' => ['title' => $event->getTitle()],
                ],
            ),
        );
    }
}
