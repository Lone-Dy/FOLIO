<?php
namespace App\Controller;

class ConditionController {
    public function index(?array $params) {
        include __DIR__.'/../../template/conditions-utilisation.php';
    }

    public function privacy(?array $params) {
        include __DIR__.'/../../template/politique-confidentialite.php';
    }

    public function mentions(?array $params) {
        include __DIR__.'/../../template/mentions-legales.php';
    }
}
?>