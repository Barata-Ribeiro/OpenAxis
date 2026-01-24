<?php

namespace App\Interfaces\Admin;

use App\Http\Requests\Admin\NotificationRequest;

interface NotifierServiceInterface
{
    /**
     * Send a notification described by the provided NotificationRequest.
     *
     * The concrete implementation is responsible for delivering the notification
     * to the intended recipients. Delivery semantics (synchronous send, queuing,
     * batching, retry policy, transport) are implementation-specific.
     *
     * Implementations SHOULD validate the request and handle sensitive data
     * according to the application's security and privacy requirements.
     *
     * @param  NotificationRequest  $request  The notification payload and delivery metadata.
     *
     * @throws \InvalidArgumentException If the provided request is invalid.
     * @throws \RuntimeException If delivery fails for implementation-specific reasons.
     *
     * @see NotificationRequest
     */
    public function sendNotification(NotificationRequest $request): void;
}
