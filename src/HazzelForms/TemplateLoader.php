<?php

namespace HazzelForms;

class TemplateLoader
{
    private $templatePath;

    protected $variables;

    public function __construct($templatePath)
    {
        $this->templatePath = $templatePath;
        $this->variables = [];
    }

    public function inject($variables = []): void
    {
        $this->variables = $variables;
    }

    public function loadTemplate($subject, $formFields = [])
    {
        // Create a lambda function to include the template file
        $templateLoader = function ($templateFile, $subject, $fields, $vars) {
            // Start output buffering
            ob_start();

            // Include the template file
            include $templateFile;

            // Return the contents of the output buffer and clean the buffer
            return ob_get_clean();
        };

        // Call the lambda function with the template file and variables
        return $templateLoader($this->templatePath, $subject, $formFields, $this->variables);
    }
}
