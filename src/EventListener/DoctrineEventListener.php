<?php
/**
 * DoctrineEventListener class.
 */

namespace App\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;

class DoctrineEventListener
{
    public function postPersist(LifecycleEventArgs $args)
    {
    }
}
