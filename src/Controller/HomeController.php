<?php
namespace App\Controller;

class HomeController {

    public function __construct() {}

    public function index(?array $params) {
        include __DIR__.'/../../template/home_page.php';
    }
}

?>