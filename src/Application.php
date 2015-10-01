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
	public function __construct($name = 'Soup', $version = '@git_tag@') {
		parent::__construct($name, $version);
	}

	/**
	 * @override
	 */
	protected function getDefaultCommands() {
		$commands = parent::getDefaultCommands();
		$commands[] = new Command\Edit();

		if (('@' . 'git_tag@') !== $this->getVersion()) {
			$command = new Amend\Command('update');
			$command->setManifestUri('@manifest_url@');

			$commands[] = $command;
		}

		return $commands;
	}
}

?>