<?php

namespace PhpHephaestus\App\Console\Command\Generate;

use PhpHephaestus\App\Tests\ScalarProps;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Tests extends Command
{
	protected static $defaultName = 'generate:tests';
	protected static $defaultDescription = 'Generate PHPUnit tests and dependencies';
	private const TEST_DIR_ARG = 'test-dir';
	private const NAMESPACE_ARG = 'namespace';
	private string $testsDir;
	private string $namespace;

	public function __construct(string $testsDir, string $namespace)
	{
		$this->testsDir = $testsDir;
		$this->namespace = $namespace;
		parent::__construct();
	}

	protected function configure(): void
	{
		$this
			->addArgument(
				self::TEST_DIR_ARG,
				InputArgument::OPTIONAL,
				'Directory to output tests and dependencies.',
				$this->testsDir
			)
			->addArgument(
				self::NAMESPACE_ARG,
				InputArgument::OPTIONAL,
				'Namespace for output tests and dependencies.',
				$this->namespace
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$testsDir = $input->getArgument(self::TEST_DIR_ARG);
		$namespace = $input->getArgument(self::NAMESPACE_ARG);

		ScalarProps::generate($testsDir, $namespace);

		return Command::SUCCESS;
	}
}
