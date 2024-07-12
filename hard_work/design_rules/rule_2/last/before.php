<?php
class Session
{
    private string $sessionCookieName = '_ga_';

    private string $sessionID;

    public function __construct()
    {
        $this->sessionCookieName = '_ga_' . substr(WP_GA4_ID, 2);
        $this->setSessionID();
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

    private function setSessionID(): void
    {
        if (empty($_COOKIE[$this->sessionCookieName])) {
            $this->sessionID = $this->generateSessionID();

            return;
        }
        $sessionID = esc_html($_COOKIE[$this->sessionCookieName]);
        $parts     = explode('.', $sessionID);
        if (count($parts) !== 9) {
            $this->sessionID = $this->generateSessionID();

            return;
        }
        $this->sessionID = $parts[2];
    }

    /**
     * @throws \Exception
     */
    private function generateSessionID(): string
    {
        return random_int(1000000000, 9999999999);
    }
}