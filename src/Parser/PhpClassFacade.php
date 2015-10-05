<?php
namespace Famelo\Soup\Parser;

use Famelo\Soup\Parser\Printer\TYPO3Printer;
use Famelo\Soup\Recipes\TYPO3;
use PhpParser\Builder\Method;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

/*
 * This file belongs to the package "Famelo Soup".
 * See LICENSE.txt that was shipped with this package.
 */

class PhpClassFacade {
	/**
	 * @var string
	 */
	protected $filepath;

	/**
	 * @var object
	 */
	protected $parser;

	public function __construct($filepath = NULL) {
		$this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
		$this->statements = $this->parser->parse(file_get_contents($filepath));
	}

	public function save() {
		$prettyPrinter = new TYPO3Printer;

		try {
			$code = $prettyPrinter->prettyPrint($this->statements);
			// echo $code;
		} catch (Error $e) {
			echo 'Parse Error: ', $e->getMessage();
		}
	}

	public function addMethod($name) {
		$factory = new BuilderFactory;
		// $factory->method($name)
		//
	}

	public function getMethods() {
		$namespaceStatement = $this->statements[0];
		foreach ($namespaceStatement->stmts as $statement) {
			if ($statement instanceof \PhpParser\Node\Stmt\Class_) {
				$classNamespace = $statement;
				break;
			}
		}

		$methods = array();
		foreach ($classNamespace->stmts as $statement) {
			if ($statement instanceof \PhpParser\Node\Stmt\ClassMethod) {
				$methods[] = $statement;
			}
		}
		var_dump($methods);
		return $methods;
	}

	// public function getStatement($statements, $className) {
	// 	if ($statements[0] instanceof \PhpParser\Node\Stmt\Namespace_) {
	// 		return $this->getStatement($statements[0]->stmts, $className);
	// 	}

	// 	foreach ($stmts as $stmt) {
	// 		if ($stmt instanceof \PhpParser\Node\Stmt\Class_) {
	// 			return $stmt;
	// 		}
	// 	}
	// }

}