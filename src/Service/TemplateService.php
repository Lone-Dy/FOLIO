<?php
namespace App\Service;

class TemplateService {
    public function render(string $templatePath, array $params = []): void {
        extract($params);
        include __DIR__ . '/../../template/' . $templatePath;
    }
}
?>