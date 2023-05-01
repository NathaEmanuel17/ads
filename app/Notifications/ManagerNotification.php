<?php

namespace App\Notifications;

use CodeIgniter\Config\Services;
use CodeIgniter\Email\Email;
use CodeIgniter\I18n\Time;

class ManagerNotification
{
    /** @var string */
    protected $message;

    /** @var Email */
    protected $service;

    /**
     * Instance verification notification.
     */
    public function __construct(string $message)
    {
        $this->message   = $message;
        $this->service = Services::email();
    }

    /**
     * Sending email verification.
     *
     * @return bool
     */
    public function send()
    {
        $this->service->setFrom('no-reply@ads.com', env('APP_NAME'));
        $this->service->setTo(get_superadmin()->email);
        $this->service->setSubject(env('APP_NAME'));
        $this->service->setMessage($this->message);
        $this->service->setMailType('html');
        
        if (! $this->service->send()) {
            log_message('error', $this->service->printDebugger());

            return false;
        }

        return true;
    }
}