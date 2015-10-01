<?php

namespace Famelo\Soup\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * Patch command.
 *
 */
class Edit extends Command {

	/**
	 * The output handler.
	 *
	 * @var OutputInterface
	 */
	private $output;

	/**
	 * @var string
	 */
	protected $baseDir;

	/**
	 * @override
	 */
	protected function configure() {
		parent::configure();
		$this->setName('edit');
		$this->setDescription('Scan directory for anomalies');

		$this->addArgument('path', InputArgument::REQUIRED);

		// $this->addOption('add-getter-setter', FALSE, InputOption::VALUE_NONE,
		// 	'Adds missing getters and setters'
		// );
	}

	/**
	 * @override
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$this->output = $output;
		$this->input = $input;

		$finder = new Finder();
		$finder->files()->in($path);
    }
}

?>