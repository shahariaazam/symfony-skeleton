<?php
/**
 * UserChangedNotifier class.
 */

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UserEntityLifecycleListener
{
    /**
     * After updating any information for User entity.
     */
    public function postUpdate(User $user, LifecycleEventArgs $event)
    {
    }

    /**
     * After updating any information for User entity.
     */
    public function preUpdate(User $user, LifecycleEventArgs $event)
    {
    }

    /**
     * After creating any information for User entity.
     */
    public function postPersist(User $user, LifecycleEventArgs $event)
    {
    }

    /**
     * After creating any information for User entity.
     */
    public function prePersist(User $user, LifecycleEventArgs $event)
    {
    }

    public function preRemove(User $user, LifecycleEventArgs $event)
    {
    }

    public function postRemove(User $user, LifecycleEventArgs $event)
    {
    }
}
