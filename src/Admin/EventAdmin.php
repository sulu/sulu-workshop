<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Event;
use App\Entity\EventRegistration;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\Routing\RouteBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\Routing\RouteCollection;
use Sulu\Bundle\AdminBundle\Admin\Routing\TogglerToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\Routing\ToolbarAction;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;

class EventAdmin extends Admin
{
    const EVENT_LIST_KEY = 'events';

    const EVENT_FORM_KEY = 'event_details';

    const EVENT_REGISTRATION_LIST_KEY = 'event_registrations';

    const EVENT_LIST_ROUTE = 'app.events_list';

    const EVENT_ADD_FORM_ROUTE = 'app.event_add_form';

    const EVENT_EDIT_FORM_ROUTE = 'app.event_edit_form';

    /**
     * @var RouteBuilderFactoryInterface
     */
    private $routeBuilderFactory;

    /**
     * @var WebspaceManagerInterface
     */
    private $webspaceManager;

    public function __construct(
        RouteBuilderFactoryInterface $routeBuilderFactory,
        WebspaceManagerInterface $webspaceManager
    ) {
        $this->routeBuilderFactory = $routeBuilderFactory;
        $this->webspaceManager = $webspaceManager;
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        // Configure a NavigationItem without a Route
        $module = new NavigationItem('app.events');
        $module->setPosition(40);
        $module->setIcon('fa-calendar');

        // Configure a NavigationItem with a Route
        $events = new NavigationItem('app.events');
        $events->setPosition(10);
        $events->setMainRoute(static::EVENT_LIST_ROUTE);

        $module->addChild($events);

        $navigationItemCollection->add($module);
    }

    public function configureRoutes(RouteCollection $routeCollection): void
    {
        $locales = $this->webspaceManager->getAllLocales();

        // Configure Event List View
        $listToolbarActions = [new ToolbarAction('sulu_admin.add'), new ToolbarAction('sulu_admin.delete')];
        $listRoute = $this->routeBuilderFactory->createListRouteBuilder(self::EVENT_LIST_ROUTE, '/events/:locale')
            ->setResourceKey(Event::RESOURCE_KEY)
            ->setListKey(self::EVENT_LIST_KEY)
            ->setTitle('app.events')
            ->addListAdapters(['table'])
            ->addLocales($locales)
            ->setDefaultLocale($locales[0])
            ->setAddRoute(static::EVENT_ADD_FORM_ROUTE)
            ->setEditRoute(static::EVENT_EDIT_FORM_ROUTE)
            ->addToolbarActions($listToolbarActions);
        $routeCollection->add($listRoute);

        // Configure Event Add View
        $addFormRoute = $this->routeBuilderFactory->createResourceTabRouteBuilder(self::EVENT_ADD_FORM_ROUTE, '/events/:locale/add')
            ->setResourceKey(Event::RESOURCE_KEY)
            ->setBackRoute(static::EVENT_LIST_ROUTE)
            ->addLocales($locales);
        $routeCollection->add($addFormRoute);

        $addDetailsFormRoute = $this->routeBuilderFactory->createFormRouteBuilder(self::EVENT_ADD_FORM_ROUTE . '.details', '/details')
            ->setResourceKey(Event::RESOURCE_KEY)
            ->setFormKey(self::EVENT_FORM_KEY)
            ->setTabTitle('sulu_admin.details')
            ->setEditRoute(static::EVENT_EDIT_FORM_ROUTE)
            ->addToolbarActions([new ToolbarAction('sulu_admin.save')])
            ->setParent(static::EVENT_ADD_FORM_ROUTE);
        $routeCollection->add($addDetailsFormRoute);

        // Configure Event Edit View
        $editFormRoute = $this->routeBuilderFactory->createResourceTabRouteBuilder(static::EVENT_EDIT_FORM_ROUTE, '/events/:locale/:id')
            ->setResourceKey(Event::RESOURCE_KEY)
            ->setBackRoute(static::EVENT_LIST_ROUTE)
            ->setTitleProperty('title')
            ->addLocales($locales);
        $routeCollection->add($editFormRoute);

        $formToolbarActions = [
            new ToolbarAction('sulu_admin.save'),
            new ToolbarAction('sulu_admin.delete'),
            new TogglerToolbarAction(
                'app.enable_event',
                'enabled',
                'enable',
                'disable'
            ),
        ];
        $editDetailsFormRoute = $this->routeBuilderFactory->createFormRouteBuilder(static::EVENT_EDIT_FORM_ROUTE . '.details', '/details')
            ->setResourceKey(Event::RESOURCE_KEY)
            ->setFormKey(self::EVENT_FORM_KEY)
            ->setTabTitle('sulu_admin.details')
            ->addToolbarActions($formToolbarActions)
            ->setParent(static::EVENT_EDIT_FORM_ROUTE);
        $routeCollection->add($editDetailsFormRoute);

        $editDetailsFormRoute = $this->routeBuilderFactory->createListRouteBuilder(static::EVENT_EDIT_FORM_ROUTE . '.registrations', '/registrations')
            ->setResourceKey(EventRegistration::RESOURCE_KEY)
            ->setListKey(self::EVENT_REGISTRATION_LIST_KEY)
            ->setTabTitle('app.registrations')
            ->addRouterAttributesToListStore(['id' => 'eventId'])
            ->addListAdapters(['table'])
            ->addToolbarActions([])
            ->setUserSettingsKey('event_registrations')
            ->setParent(static::EVENT_EDIT_FORM_ROUTE);
        $routeCollection->add($editDetailsFormRoute);
    }
}
