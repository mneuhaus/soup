<?php

require_once(__DIR__ . '/../vendor/autoload.php');

$app = new \Slim\Slim();

$app->get('/', function () {
    fluid('index');
});

$app->get('/hello/:name', function ($name) {
    echo "Hello, $name";
});

function fluid($template, $variables = array()) {
    $paths = new \TYPO3Fluid\Fluid\View\TemplatePaths();
    // $paths->setTemplateRootPaths(array(__DIR__ . '/../Templates/'));
    $paths->setLayoutRootPaths(array(__DIR__ . '/../Layouts/'));
    $paths->setPartialRootPaths(array(__DIR__ . '/../Partials/'));

    $parts = explode('/', $template);
    array_walk($parts, function(&$value, $key){
        $value = ucfirst($value);
    });
    $path = implode('/', $parts);
    $templateFile = __DIR__ . '/../Templates/' . $path . '.html';
    $paths->setTemplatePathAndFilename($templateFile);

    $view = new \TYPO3Fluid\Fluid\View\TemplateView($paths);
    $view->assignMultiple($variables);

    // $processor = new \TYPO3Fluid\Fluid\Core\Parser\TemplateProcessor\UnknownNamespaceDetectionTemplateProcessor();
    // $view->setTemplateProcessors(array(
    //     $processor
    // ));

    echo $view->render();

    // $this->view->getViewHelperResolver()->registerNamespace('e', 'Mneuhaus\\Expose\\ViewHelpers');

    // $variableProvider = new ExposeVariableProvider();
    // $this->view->getRenderingContext()->setVariableProvider($variableProvider);

    // $viewHelperResolver = new \Famelo\Cider\Fluid\ViewHelperResolver();
    // $view->setViewHelperResolver($viewHelperResolver);

	// $view->assign('foobar', 'MVC template');
	// echo $view->render('Default');
}

$app->run();