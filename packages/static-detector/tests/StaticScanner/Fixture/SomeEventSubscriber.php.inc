<?php

declare(strict_types=1);

namespace Migrify\StaticDetector\Tests\StaticScanner\Fixture;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class SomeEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
    }
}
