<?php

namespace Famelo\Soup;

use KevinGH\Amend;
use Famelo\Soup\Command;
use Symfony\Component\Console\Application as Base;

/**
 * Sets up the application.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class Application extends Base {

	/**
	 * @override
	 */
	public function __construct($name = 'Soup', $version = 'master') {
		parent::__construct($name, $version);
	}

	/**
	 * @override
	 */
	protected function getDefaultCommands() {
		$commands = parent::getDefaultCommands();
		$commands[] = new Command\Edit();

		if ('master' !== $this->getVersion()) {
			$command = new Amend\Command('update');
			$command->setManifestUri('https://raw.github.com/mneuhaus/soup/master/releases.json');

			$commands[] = $command;
		}

		return $commands;
	}

	/**
	 * @override
	 */
	protected function getDefaultHelperSet() {
		$helperSet = parent::getDefaultHelperSet();
		if ('master' !== $this->getVersion()) {
			$helperSet->set(new Amend\Helper());
		}
		return $helperSet;
	}
}

?>