<?php
namespace App\Controller;

class HomeController {

    public function __construct() {}

    // J'ajoute un null pour donner un valeur par défaut. 
    public function index(?array $params = null) {
        include __DIR__.'/../../template/home_page.php';
    }
}
?>