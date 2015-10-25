<?php

namespace Famelo\Soup\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

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

		// $this->addArgument('path', InputArgument::REQUIRED);

		$this->addOption('--throttle', FALSE, InputOption::VALUE_NONE,
			'Force process throttling to make the integrated webserver to behave (needs cputhrottle installed)'
		);
	}

	/**
	 * @override
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$this->output = $output;
		$this->input = $input;

		$tmpRouter = tempnam(sys_get_temp_dir(), 'soup-router') . '.php';
		file_put_contents($tmpRouter, '<?php
$possibleFilePath = "' . BOX_PATH . '/Resources/Assets" . $_SERVER["REQUEST_URI"];
if (file_exists($possibleFilePath) & !is_dir($possibleFilePath)) {
	$types = array(
		"css" => "Content-Type: text/css",
		"js" => "Content-Type: text/javascript"
	);
	$extension = pathinfo($possibleFilePath, PATHINFO_EXTENSION);
	if (isset($types[$extension])) {
		header($types[$extension]);
	}
	readfile($possibleFilePath);
} else {
	require("' . BOX_PATH . '/src/Bootstrap.php");
}
		');

		$process = new Process('php -S localhost:1716 ' . $tmpRouter);
		$process->start();

		$output->writeln('server running on http://localhost:1716 (ctrl+c to quit)');

		while ($process->isRunning()) {
			// this is just a keep-alive,
			// if we don't sleep here the cpu process goes crazy without anything
			// to do, since we have to wait, we can wait forever as well
			sleep(PHP_INT_MAX);
		}
    }
}

?>