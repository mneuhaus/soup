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

class Model extends AbstractIngredient {
	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var array
	 */
	public $fields = array();

	/**
	 * @var array
	 */
	static protected $paths = array(
		'/Classes/Domain/Model/'
	);

	public function __construct($filepath = NULL) {
		if ($filepath !== NULL) {
			$facade = new ClassFacade($filepath);
			$this->name = $facade->getName();
			$this->fields = array(
				array(
					'label' => 'Model Name',
					'control' => 'text',
					'value' => $facade->getName()
				)
			);
		}
	}

	public function save($arguments) {
		// ->makePublic()
		// ->addParam($factory->param('someParam')->setTypeHint('SomeClass'))
		// ->setDocComment('');
	}
}