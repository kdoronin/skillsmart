<?php

namespace GA4\Analytics;

defined('ABSPATH') or die('Not Authorized!');


/**
 * Class AnalyticsSingleton
 * @package GA4\Analytics
 * @description Singleton class for Google Analytics 4
 * Has only one instance. Used for collect all GA4 data
 * You can add data to backend and frontend events.
 * User, Client and Session data provided by separate classes.
 * You can use addBackendEvent and addFrontendEvent methods to add events.
 * You can use removeBackendEvent method to remove event by name.
 * For sending backend events to GA4 use send method.
 * For output frontend events to HTML use outputScript method.
 */
class AnalyticsSingleton
{
    private static $instance = null;
    private array $backendEvents = ['events' => []];
    private array $frontendEvents = ['events' => []];
    private User $user;
    public Client $client;
    public Session $session;

    private function __construct()
    {
        $this->user    = new User();
        $this->client  = new Client();
        $this->session = new Session();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function addBackendEvent(array $event): void
    {
        $event["params"] = array_merge($event["params"], $this->session->prepare());
        $this->backendEvents['events'][] = $event;
    }

    public function removeBackendEvent(string $name): void
    {
        foreach ($this->backendEvents['events'] as $key => $event) {
            if ($event['name'] === $name) {
                unset($this->backendEvents['events'][$key]);
                $this->backendEvents['events'] = array_values($this->backendEvents['events']);

                return;
            }
        }
    }

    public function addFrontendEvent(array $event): void
    {
        $this->frontendEvents['events'][] = $event;
    }

    public function send(): void
    {
        if (empty($this->backendEvents['events'])) {
            return;
        }
        $data    = $this->prepareBackendData();
        $request = new Request($data);
        $request->send();
    }

    public function outputScript(): void
    {
        if (empty($this->frontendEvents['events'])) {
            return;
        }
        $script = new Script($this->frontendEvents);
        $script->output();
    }

    private function prepareBackendData(): array
    {
        return array_merge(
            $this->client->prepare(),
            $this->user->prepare(),
            $this->backendEvents
        );
    }
}