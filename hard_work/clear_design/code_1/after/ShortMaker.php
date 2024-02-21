<?php

namespace WowvendorLinkShortener\Includes;

class ShortMaker
{
    private LinkDatabase $DB;

    public function __construct()
    {
        $this->DB = new LinkDatabase();
    }

    public function addShort(string $link): string
    {
        $key = $this->generateKey();
        $this->DB->addLink($key, $link);
        return $key;
    }

    private function generateKey(): string
    {
        $keys = $this->DB->getAllValidKeys();
        do {
            $key = $this->generateRandomString();
        } while (in_array($key, $keys));
        return $key;
    }

    private function generateRandomString(): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $keyLength = 6;
        $key = '';
        for ($i = 0; $i < $keyLength; $i++) {
            $key .= $characters[rand(0, $charactersLength - 1)];
        }
        return $key;
    }

}