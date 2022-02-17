<?php
namespace App\Listeners;

use App\Events\AdminAddedEvent;

class NotifyAddedAdminListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  AdminAddedEvent  $event
     * @return void
     */
    public function handle(AdminAddedEvent $event)
    {
        $user = $event->user;
    }
}
