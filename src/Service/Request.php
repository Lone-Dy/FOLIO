<?php

namespace App\Service;

class Request {
    // public function getPost(): array {
    //     return $_POST;
    // }

    public function getParam(string $key, $default = null) {
        return $_POST[$key] ?? $default;
    }

    public function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}