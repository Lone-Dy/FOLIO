<?php
namespace App\Controller;

use App\Service\ConditionService;

class ConditionController {

    private ConditionService $conditionService;

    public function index(?array $params = null) {
        include __DIR__.'/../../template/conditions-utilisation.php';
    }

    public function privacy(?array $params = null) {
        include __DIR__.'/../../template/politique-confidentialite.php';
    }

    public function mentions(?array $params = null) {
        include __DIR__.'/../../template/mentions-legales.php';
    }
}
?>