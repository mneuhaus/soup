<?php
namespace Famelo\Soup\Ingredients\TYPO3;

use Famelo\Archi\Php\ClassFacade;
use Famelo\Soup\Utility\String;
use Symfony\Component\Finder\Finder;

/*
 * This file belongs to the package "Famelo Soup".
 * See LICENSE.txt that was shipped with this package.
 */

class Controller {
	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var array
	 */
	public $fields = array();

	public function __construct($filepath = NULL) {
		if ($filepath !== NULL) {
			$facade = new ClassFacade($filepath);
			$this->name = $facade->getName();
			foreach ($facade->getMethods() as $method) {
				if (!String::endsWith($method->getName(), 'Action')) {
					continue;
				}
				$actions[] = array(
					array(
						'label' => 'Name',
						'control' => 'text',
						'value' => String::cutSuffix($method->getName(), 'Action')
					)
				);
			}
			$this->fields = array(
				array(
					'label' => 'Controller Name',
					'control' => 'text',
					'value' => String::cutSuffix($facade->getName(), 'Controller')
				),
				array(
					'label' => 'Actions',
					'control' => 'repeater',
					'items' => $actions
				)
			);
		}
	}

	public static function getExistingInstances() {
		$finder = new Finder();
		$controllerFiles = $finder->files()->in(WORKING_DIRECTORY . '/Classes/Controller/');
		$instances = array();
		foreach ($controllerFiles as $controllerFile) {
			$ingredientClassName = self::class;
			$instances[] = new $ingredientClassName($controllerFile->getRealPath());
		}
		return $instances;
	}

	public function save($arguments) {
		// ->makePublic()
		// ->addParam($factory->param('someParam')->setTypeHint('SomeClass'))
		// ->setDocComment('');
	}
}