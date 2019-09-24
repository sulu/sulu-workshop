<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Location;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;

class LocationAdmin extends Admin
{
    const LOCATION_LIST_KEY = 'locations';

    const LOCATION_LIST_VIEW = 'app.locations_list';

    const LOCATION_ADD_FORM_VIEW = 'app.location_add_form';

    const LOCATION_EDIT_FORM_VIEW = 'app.location_edit_form';

    /**
     * @var ViewBuilderFactoryInterface
     */
    private $viewBuilderFactory;

    public function __construct(ViewBuilderFactoryInterface $viewBuilderFactory)
    {
        $this->viewBuilderFactory = $viewBuilderFactory;
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        $module = $navigationItemCollection->get('app.events');

        $locations = new NavigationItem('app.locations');
        $locations->setPosition(10);
        $locations->setView(static::LOCATION_LIST_VIEW);

        $module->addChild($locations);
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        $listToolbarActions = [
            new ToolbarAction('sulu_admin.add'),
            new ToolbarAction('sulu_admin.delete'),
        ];
        $listView = $this->viewBuilderFactory->createListViewBuilder(self::LOCATION_LIST_VIEW, '/locations')
            ->setResourceKey(Location::RESOURCE_KEY)
            ->setListKey(self::LOCATION_LIST_KEY)
            ->setTitle('app.locations')
            ->addListAdapters(['table'])
            ->setAddView(static::LOCATION_ADD_FORM_VIEW)
            ->setEditView(static::LOCATION_EDIT_FORM_VIEW)
            ->addToolbarActions($listToolbarActions);
        $viewCollection->add($listView);

        $addFormView = $this->viewBuilderFactory->createResourceTabViewBuilder(self::LOCATION_ADD_FORM_VIEW, '/locations/add')
            ->setResourceKey('locations')
            ->setBackView(static::LOCATION_LIST_VIEW);
        $viewCollection->add($addFormView);

        $addDetailsFormView = $this->viewBuilderFactory->createFormViewBuilder(self::LOCATION_ADD_FORM_VIEW . '.details', '/details')
            ->setResourceKey('locations')
            ->setFormKey('location_details')
            ->setTabTitle('sulu_admin.details')
            ->setEditView(static::LOCATION_EDIT_FORM_VIEW)
            ->addToolbarActions([new ToolbarAction('sulu_admin.save')])
            ->setParent(static::LOCATION_ADD_FORM_VIEW);
        $viewCollection->add($addDetailsFormView);

        $editFormView = $this->viewBuilderFactory->createResourceTabViewBuilder(static::LOCATION_EDIT_FORM_VIEW, '/locations/:id')
            ->setResourceKey('locations')
            ->setBackView(static::LOCATION_LIST_VIEW)
            ->setTitleProperty('title');
        $viewCollection->add($editFormView);

        $formToolbarActions = [
            new ToolbarAction('sulu_admin.save'),
            new ToolbarAction('sulu_admin.delete'),
        ];
        $editDetailsFormView = $this->viewBuilderFactory->createFormViewBuilder(static::LOCATION_EDIT_FORM_VIEW . '.details', '/details')
            ->setResourceKey('locations')
            ->setFormKey('location_details')
            ->setTabTitle('sulu_admin.details')
            ->addToolbarActions($formToolbarActions)
            ->setParent(static::LOCATION_EDIT_FORM_VIEW);
        $viewCollection->add($editDetailsFormView);
    }
}
