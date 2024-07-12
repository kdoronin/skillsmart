<?php
class Session
{
    private string $sessionCookieName = '_ga_';

    private string $sessionID;

    public function __construct(string $sessionID = '')
    {
        $this->sessionCookieName = '_ga_' . substr(WP_GA4_ID, 2);
        $this->sessionID = $sessionID;
    }

    public function prepare(): array
    {
        return [
            'session_id' => $this->sessionID,
        ];
    }

    public function updateSessionID(string $sessionID): void
    {
        $this->sessionID = $sessionID;
    }

    public function getSessionID(): string
    {
        return $this->sessionID;
    }
}