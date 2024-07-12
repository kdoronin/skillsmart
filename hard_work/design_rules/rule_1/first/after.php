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
        $this->validateUserId($user_id);
        $this->analytics->removeBackendEvent('login');
        $event = EventFactory::create('login', ['method' => $provider->getLabel(), 'user_id' => $user_id]);
        $this->analytics->addBackendEvent($event->prepare());
    }

    public function NSLSignUp(int $user_id, \NextendSocialProvider $provider): void
    {
        $this->validateUserId($user_id);
        $event = EventFactory::create('sign_up', ['method' => $provider->getLabel(), 'user_id' => $user_id]);
        $this->analytics->addBackendEvent($event->prepare());
        $this->signedUp = true;
    }

    /**
     * @throws \Exception
     */
    public function login(string $user_login, WP_User $user): void
    {
        $this->validateUserLogin($user_login);
        $event = EventFactory::create('login', ['method' => 'e-mail', 'user_id' => $user->ID]);
        $this->analytics->addBackendEvent($event->prepare());
    }

    public function signUp(int $user_id): void
    {
        $this->validateUserId($user_id);
        if ($this->signedUp) {
            return;
        }
        $event = EventFactory::create('sign_up', ['method' => 'e-mail', 'user_id' => $user_id]);
        $this->analytics->addBackendEvent($event->prepare());
    }

    /**
     * Validates if a user ID exists in the system.
     *
     * @param int $user_id
     * @throws \InvalidArgumentException
     */
    private function validateUserId(int $user_id): void
    {
        if (!get_userdata($user_id)) {
            throw new \InvalidArgumentException('Invalid user_id passed.');
        }
    }

    /**
     * Validates if a user login exists in the system.
     *
     * @param string $user_login
     * @throws \InvalidArgumentException
     */
    private function validateUserLogin(string $user_login): void
    {
        if (!username_exists($user_login)) {
            throw new \InvalidArgumentException('Invalid user_login passed.');
        }
    }

}