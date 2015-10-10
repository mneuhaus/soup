<?php
namespace Famelo\Soup\Ingredients\TYPO3;

use Famelo\Archi\Php\ClassFacade;
use Famelo\Soup\Core\Ingredients\AbstractIngredient;
use Famelo\Soup\Utility\String;
use Symfony\Component\Finder\Finder;

/*
 * This file belongs to the package "Famelo Soup".
 * See LICENSE.txt that was shipped with this package.
 */

class Controller extends AbstractIngredient{
	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var ClassFacade
	 */
	public $facade;

	/**
	 * @var string
	 */
	protected $filepath;

	/**
	 * @var array
	 */
	static protected $paths = array(
		'/Classes/Controller/'
	);

	public function __construct($filepath = NULL) {
		if ($filepath !== NULL) {
			$this->facade = new ClassFacade($filepath);
			$this->name = $this->facade->getName();
			$this->filepath = $filepath;
		}
	}

	public function getFilepath() {
		return $this->filepath;
	}

	public function getActions() {
		$actions = array();
		foreach ($this->facade->getMethods() as $method) {
			if (!String::endsWith($method->getName(), 'Action')) {
				continue;
			}
			$actions[$method->getName()] = $method;
		}
		return $actions;
	}

	public function save($arguments) {
		$existingActions = $this->getActions();
		foreach ($arguments['actions'] as $method => $data) {
			if (is_array($data) && isset($data['_remove'])) {
				$this->facade->removeMethod($method);
			} else if (isset($existingActions[$method])) {
				if ($method !== String::addSuffix($data, 'Action')) {
					$this->facade->renameMethod($method, String::addSuffix($data, 'Action'));
				}
			} else {
				$this->facade->addMethod(String::addSuffix($data, 'Action'));
			}
		}
		$this->facade->save();
		// ->makePublic()
		// ->addParam($factory->param('someParam')->setTypeHint('SomeClass'))
		// ->setDocComment('');
	}
}