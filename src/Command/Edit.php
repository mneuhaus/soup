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

		$tmpRouter = tempnam(sys_get_temp_dir(), 'soup-router') . '.php';
		file_put_contents($tmpRouter, '<?php require("' . BOX_PATH . '/src/Bootstrap.php");');

		$process = new Process('php -S localhost:1716 ' . $tmpRouter);
		$process->start();

		$output->writeln('server running on http://localhost:1716 (ctrl+c to quit)');

		shell_exec('open http://localhost:1716');

		while ($process->isRunning()) {}
    }
}

?>