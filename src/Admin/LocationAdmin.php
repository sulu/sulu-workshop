<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Location;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;

class LocationAdmin extends Admin
{
    const LOCATION_LIST_KEY = 'locations';

    const LOCATION_LIST_VIEW = 'app.locations_list';

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
        $listView = $this->viewBuilderFactory->createListViewBuilder(self::LOCATION_LIST_VIEW, '/locations')
            ->setResourceKey(Location::RESOURCE_KEY)
            ->setListKey(self::LOCATION_LIST_KEY)
            ->setTitle('app.locations')
            ->addListAdapters(['table'])
            ->addToolbarActions([]);
        $viewCollection->add($listView);
    }
}
