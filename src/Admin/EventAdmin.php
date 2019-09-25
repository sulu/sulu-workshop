<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Event;
use App\Entity\EventRegistration;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\TogglerToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;

class EventAdmin extends Admin
{
    const EVENT_LIST_KEY = 'events';

    const EVENT_FORM_KEY = 'event_details';

    const EVENT_LIST_VIEW = 'app.events_list';

    const EVENT_REGISTRATION_LIST_KEY = 'event_registrations';

    const EVENT_ADD_FORM_VIEW = 'app.event_add_form';

    const EVENT_EDIT_FORM_VIEW = 'app.event_edit_form';

    /**
     * @var ViewBuilderFactoryInterface
     */
    private $viewBuilderFactory;

    /**
     * @var WebspaceManagerInterface
     */
    private $webspaceManager;

    public function __construct(
        ViewBuilderFactoryInterface $viewBuilderFactory,
        WebspaceManagerInterface $webspaceManager
    ) {
        $this->viewBuilderFactory = $viewBuilderFactory;
        $this->webspaceManager = $webspaceManager;
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        $module = new NavigationItem('app.events');
        $module->setPosition(40);
        $module->setIcon('fa-calendar');

        // Configure a NavigationItem with a View
        $events = new NavigationItem('app.events');
        $events->setPosition(10);
        $events->setView(static::EVENT_LIST_VIEW);

        $module->addChild($events);

        $navigationItemCollection->add($module);
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        $locales = $this->webspaceManager->getAllLocales();

        // Configure Event List View
        $listToolbarActions = [new ToolbarAction('sulu_admin.add'), new ToolbarAction('sulu_admin.delete')];
        $listView = $this->viewBuilderFactory->createListViewBuilder(self::EVENT_LIST_VIEW, '/events/:locale')
            ->setResourceKey(Event::RESOURCE_KEY)
            ->setListKey(self::EVENT_LIST_KEY)
            ->setTitle('app.events')
            ->addListAdapters(['table'])
            ->addLocales($locales)
            ->setDefaultLocale($locales[0])
            ->setAddView(static::EVENT_ADD_FORM_VIEW)
            ->setEditView(static::EVENT_EDIT_FORM_VIEW)
            ->addToolbarActions($listToolbarActions);
        $viewCollection->add($listView);

        // Configure Event Add View
        $addFormView = $this->viewBuilderFactory->createResourceTabViewBuilder(self::EVENT_ADD_FORM_VIEW, '/events/:locale/add')
            ->setResourceKey(Event::RESOURCE_KEY)
            ->setBackView(static::EVENT_LIST_VIEW)
            ->addLocales($locales);
        $viewCollection->add($addFormView);

        $addDetailsFormView = $this->viewBuilderFactory->createFormViewBuilder(self::EVENT_ADD_FORM_VIEW . '.details', '/details')
            ->setResourceKey(Event::RESOURCE_KEY)
            ->setFormKey(self::EVENT_FORM_KEY)
            ->setTabTitle('sulu_admin.details')
            ->setEditView(static::EVENT_EDIT_FORM_VIEW)
            ->addToolbarActions([new ToolbarAction('sulu_admin.save')])
            ->setParent(static::EVENT_ADD_FORM_VIEW);
        $viewCollection->add($addDetailsFormView);

        // Configure Event Edit View
        $editFormView = $this->viewBuilderFactory->createResourceTabViewBuilder(static::EVENT_EDIT_FORM_VIEW, '/events/:locale/:id')
            ->setResourceKey(Event::RESOURCE_KEY)
            ->setBackView(static::EVENT_LIST_VIEW)
            ->setTitleProperty('title')
            ->addLocales($locales);
        $viewCollection->add($editFormView);

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
        $editDetailsFormView = $this->viewBuilderFactory->createFormViewBuilder(static::EVENT_EDIT_FORM_VIEW . '.details', '/details')
            ->setResourceKey(Event::RESOURCE_KEY)
            ->setFormKey(self::EVENT_FORM_KEY)
            ->setTabTitle('sulu_admin.details')
            ->addToolbarActions($formToolbarActions)
            ->setParent(static::EVENT_EDIT_FORM_VIEW);
        $viewCollection->add($editDetailsFormView);

        $editDetailsFormView = $this->viewBuilderFactory->createListViewBuilder(static::EVENT_EDIT_FORM_VIEW . '.registrations', '/registrations')
            ->setResourceKey(EventRegistration::RESOURCE_KEY)
            ->setListKey(self::EVENT_REGISTRATION_LIST_KEY)
            ->setTabTitle('app.registrations')
            ->addRouterAttributesToListRequest(['id' => 'eventId'])
            ->addListAdapters(['table'])
            ->addToolbarActions([])
            ->setUserSettingsKey(EventRegistration::RESOURCE_KEY)
            ->setParent(static::EVENT_EDIT_FORM_VIEW);
        $viewCollection->add($editDetailsFormView);
    }
}
