<?php
class Script
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function output(): void
    {
        $script = $this->prepareScript();
        echo $script;
    }

    private function prepareScript(): string
    {
        $script = '<script>document.addEventListener("DOMContentLoaded", (event) => {';
        foreach ($this->data['events'] as $event) {
            $script .= $this->prepareEvent($event);
        }

        return $script . '});</script>';
    }

    private function prepareEvent(array $event): string
    {
        return <<<SCRIPT
        gtag('event', '{$event['name']}', { {$this->prepareParams($event['params'])} });
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