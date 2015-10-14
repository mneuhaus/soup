<?php
namespace Famelo\Soup\Typo3\Ingredients;

use Famelo\Archi\ComposerFacade;
use Famelo\Archi\Php\ClassFacade;
use Famelo\Soup\Core\Ingredients\AbstractIngredient;
use Famelo\Soup\Utility\Path;
use Famelo\Soup\Utility\String;
use Symfony\Component\Finder\Finder;

/*
 * This file belongs to the package "Famelo Soup".
 * See LICENSE.txt that was shipped with this package.
 */

class Model extends AbstractIngredient {
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

	public function __construct($filepath = NULL) {
		if ($filepath === NULL || !file_exists($filepath)) {
			$filepath = Path::joinPaths(BASE_DIRECTORY, '../Resources/CodeTemplates/Typo3/Model.php');
		} else {
			$this->filepath = $filepath;
		}
		$this->facade = new ClassFacade($filepath);
		$this->name = $this->facade->getName();
	}

	public function getArguments() {
		return array($this->filepath);
	}

	public function getProperties() {
		$properties = array();
		foreach ($this->facade->getProperties() as $property) {
			$properties[$property->getName()] = $property;
		}
		return $properties;
	}

	public function getTypeOptions() {
		return array(
			'string' => 'String',
			'boolean' => 'Boolean'
		);
	}

	public function save($arguments) {
		$className = ucfirst($arguments['name']);
		$targetFileName = 'Classes/Domain/Model/' . $className . '.php';
		if (!Path::isIdentical($targetFileName, $this->filepath) && file_exists($this->filepath)) {
			unlink($this->filepath);
		}
		$this->saveNamespace();
		$this->facade->setClassName($className);

		$existingProperties = $this->getProperties();
		foreach ($arguments['properties'] as $property => $data) {
			if (isset($data['_remove'])) {
				$this->facade->removeProperty($property);
			} else if (isset($existingProperties[$property])) {
				if ($property !== $data['name']) {
					$this->facade->renameProperty($property, $data['name']);
				}
			} else {
				$this->facade->addProperty($data['name']);
			}
		}

		$this->facade->save($targetFileName);
	}

	public function saveNamespace() {
		$composer = new ComposerFacade('composer.json');
		$namespace = $composer->getNamespace() . '\\Domain\\Model';
		$this->facade->setNamespace($namespace);
	}
}