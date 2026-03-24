<?php

namespace App\Service;

use PDO;
use Exception;

class FlashService {
    
    private const FLASH_KEY = 'flash_messages';

    public function addSuccess(string $message): void {
        $this->addMessage('success', $message);
    }

    public function addError(string $message): void {
        $this->addMessage('error', $message);
    }

    private function addMessage(string $type, string $message): void {
        $_SESSION[self::FLASH_KEY][$type][] = $message;
    }

    public function getMessages(): array {
        $messages = $_SESSION[self::FLASH_KEY] ?? [];
        unset($_SESSION[self::FLASH_KEY]);
        return $messages;
    }
}