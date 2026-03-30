<?php

namespace App\Controller;


class ConditionController {

    public function __construct() {}

    public function index(?array $params = null) {

        include __DIR__.'/../../template/conditions-utilisation.php';
        include __DIR__.'/../../template/mentions-legales.php';
        include __DIR__.'/../../template/politique-confidentialite.php';
    
    }

}
?>