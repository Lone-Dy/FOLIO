<?php

namespace App\Service;

use App\Controller\ConditionController;

use PDO;
use Exception;

class ConditionService {
    public function getConditions(): string 
    {
        return file_get_contents(__DIR__ . '/../../template/conditions-utilisation.php');
    }
}