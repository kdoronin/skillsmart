<?php

use GA4\Analytics\AnalyticsSingleton;
use GA4\Events\EventFactory;

class Account
{
    private AnalyticsSingleton $analytics;
    private bool $signedUp = false;

    public function __construct()
    {
        $this->analytics = AnalyticsSingleton::getInstance();
        add_action('nsl_login', [$this, 'NSLlogin'], 10, 2);
        add_action('nsl_register_new_user', [$this, 'NSLSignUp'], 10, 2);
        add_action('wp_login', [$this, 'login'], 10, 2);
        add_action('register_new_user', [$this, 'signUp']);
    }

    /**
     * @throws \Exception
     */
    public function NSLlogin(int $user_id, \NextendSocialProvider $provider): void
    {
        $this->analytics->removeBackendEvent('login');
        $event = EventFactory::create('login', ['method' => $provider->getLabel(), 'user_id' => $user_id]);
        $this->analytics->addBackendEvent($event->prepare());
    }

    public function NSLSignUp(int $user_id, \NextendSocialProvider $provider): void
    {
        $event = EventFactory::create('sign_up', ['method' => $provider->getLabel(), 'user_id' => $user_id]);
        $this->analytics->addBackendEvent($event->prepare());
        $this->signedUp = true;
    }

    /**
     * @throws \Exception
     */
    public function login(string $user_login, WP_User $user): void
    {
        $event = EventFactory::create('login', ['method' => 'e-mail', 'user_id' => $user->ID]);
        $this->analytics->addBackendEvent($event->prepare());
    }

    public function signUp(int $user_id): void
    {
        if ($this->signedUp) {
            return;
        }
        $event = EventFactory::create('sign_up', ['method' => 'e-mail', 'user_id' => $user_id]);
        $this->analytics->addBackendEvent($event->prepare());
    }

}