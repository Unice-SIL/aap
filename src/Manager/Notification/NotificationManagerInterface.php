<?php

namespace App\Manager\Notification;


use App\Entity\Notification;

interface NotificationManagerInterface
{
    public function create(): Notification;

    public function save(Notification $notification);

    public function update(Notification $notification);

    public function delete(Notification $notification);
}