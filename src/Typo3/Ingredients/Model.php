<?php
namespace Famelo\Soup\Typo3\Ingredients;

use Famelo\Archi\ComposerFacade;
use Famelo\Archi\Php\ClassFacade;
use Famelo\Archi\Typo3\ModelFacade;
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
		$this->filepath = $filepath;
		$this->facade = new ModelFacade($filepath);
		$this->name = $this->facade->name;
	}

	public static function getInstances() {
		$finder = new Finder();
		$files = $finder->files()->in('.')->path('Classes/Domain/Model/')->name('*.php');
		$instances = array();
		foreach ($files as $file) {
			$instances[] = new Model($file->getRealPath());
		}
		return $instances;
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
		$this->facade->name = $arguments['name'];

		$composer = new ComposerFacade('composer.json');
		$this->facade->namespace = $composer->getNamespace() . '\\Domain\\Model';

		foreach ($arguments['properties'] as $property => $data) {
			if (isset($data['_remove'])) {
				$this->facade->removeProperty($property);
			} else if (isset($existingProperties[$property])) {
				if ($property !== $data['name']) {
					$this->facade->renameProperty($property, $data['name']);
				}
			} else {
				$this->facade->addProperty($data['name'], NULL, array(
					'propertyType' => $data['type']
				));
			}
		}

		$this->facade->save();
	}
}