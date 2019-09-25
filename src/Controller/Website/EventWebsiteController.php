<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Form\EventRegistrationType;
use App\Repository\EventRegistrationRepository;
use App\Repository\EventRepository;
use Sulu\Bundle\WebsiteBundle\Resolver\TemplateAttributeResolverInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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

        $eventRegistrationRepository = $this->get(EventRegistrationRepository::class);
        $registration = $eventRegistrationRepository->create($event);
        $form = $this->createForm(EventRegistrationType::class, $registration);
        $form->add(
            'submit',
            SubmitType::class,
            [
                'label' => 'Create',
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventRegistrationRepository->save($registration);

            return $this->redirectToRoute(
                'app.event',
                [
                    'id' => $event->getId(),
                    'success' => true,
                ]
            );
        }

        return $this->render(
            'events/index.html.twig',
            $this->get(TemplateAttributeResolverInterface::class)->resolve(
                [
                    'event' => $event,
                    'success' => $request->query->get('success'),
                    'form' => $form->createView(),
                    'content' => ['title' => $event->getTitle()],
                ]
            )
        );
    }

    /**
     * @return mixed[]
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                EventRepository::class,
                EventRegistrationRepository::class,
                TemplateAttributeResolverInterface::class,
            ]
        );
    }
}
