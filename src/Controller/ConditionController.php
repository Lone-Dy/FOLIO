<?php
namespace App\Controller;

use App\Service\TemplateService;

class ConditionController {
    private TemplateService $templateService;

    public function __construct(TemplateService $templateService) {
        $this->templateService = $templateService;
    }

    public function index(): void {
        $this->templateService->render('conditions-utilisation.php');
    }

    public function privacy(): void {
        $this->templateService->render('politique-confidentialite.php');
    }

    public function mentions(): void {
        $this->templateService->render('mentions-legales.php');
    }
}
?>