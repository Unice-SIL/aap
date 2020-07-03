<?php


namespace App\Manager\Notification;


use App\Entity\Notification;

abstract class AbstractNotificationManager implements NotificationManagerInterface
{

    public function create(): Notification
    {
        return new Notification();
    }

    public abstract function save(Notification $notification);

    public abstract function update(Notification $notification);

    public abstract function delete(Notification $notification);


}