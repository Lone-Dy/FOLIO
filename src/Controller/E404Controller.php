<?php
namespace App\Controller;

class E404Controller {

    public function __construct() {}

    public function index(?array $params) {
        include __DIR__.'/../../template/error_404.php';
    }
}

?>