<?php

declare(strict_types=1);

namespace Filament\Notifications\Testing;

use Filament\Notifications\Notification;
use Pest\Plugin;

/**
 * @return TestCall | TestCase | mixed
 */
function assertNotified(Notification | string $notification = null)
{
    Notification::assertNotified($notification);

    return test();
}
