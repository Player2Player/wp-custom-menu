<?php

use P2P\Amelia\Infrastructure\Container;

Container::instance()->set('location.repository', 'P2P\Amelia\Repository\LocationRepository');
Container::instance()->set('category.repository', 'P2P\Amelia\Repository\CategoryRepository');
Container::instance()->set('provider.repository', 'P2P\Amelia\Repository\ProviderRepository');
Container::instance()->set('event.repository', 'P2P\Amelia\Repository\EventRepository');
