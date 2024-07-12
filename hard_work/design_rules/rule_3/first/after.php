<?php

class Script
{
    private EventCollection $eventCollection;

    public function __construct(EventCollection $eventCollection)
    {
        $this->eventCollection = $eventCollection;
    }

    public function output(): void
    {
        $script = $this->prepareScript();
        echo $script;
    }

    private function prepareScript(): string
    {
        $script = '<script>document.addEventListener("DOMContentLoaded", (event) => {';
        foreach ($this->eventCollection->getEvents() as $event) {
            $script .= $this->prepareEvent($event);
        }

        return $script . '});</script>';
    }

    private function prepareEvent(Event $event): string
    {
        $eventData = $event->toArray();
        return <<<SCRIPT
        gtag('event', '{$eventData['name']}', { {$this->prepareParams($eventData['params'])} });
        SCRIPT;
    }

    private function prepareParams(array $params): string
    {
        $script = '';
        foreach ($params as $key => $value) {
            $value  = json_encode($value);
            $script .= "'{$key}': {$value},";
        }

        return $script;
    }
}