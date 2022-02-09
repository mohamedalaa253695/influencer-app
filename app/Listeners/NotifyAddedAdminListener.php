<?php
namespace App\Listeners;

use App\Events\AdminAddedEvent;
use Illuminate\Support\Facades\Mail;

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
        Mail::send('admin.adminAdded', ['order' => $user], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('You have been added to the Admin App!');
        });
    }
}
