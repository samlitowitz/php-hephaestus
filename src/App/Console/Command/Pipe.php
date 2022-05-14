<?php

namespace PhpHephaestus\App\Console\Command;

use InvalidArgumentException;
use PhpHephaestus\App\Console\CLI;
use PhpHephaestus\App\Console\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Pipe extends Command
{
	protected static $defaultName = 'pipe';
	protected static $defaultDescription = 'Pipe sources to sinks';
	private const SOURCE_ARG = 'source';
	private const TARGET_ARG = 'target';

	protected function configure(): void
	{
		$this
			->addArgument(
				self::SOURCE_ARG,
				InputArgument::REQUIRED,
				'Source to supply IR'
			)
			->addArgument(
				self::TARGET_ARG,
				InputArgument::REQUIRED,
				'Sink to drain IR'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$cfgFile = \file_get_contents($input->getArgument(CLI::CONFIG_ARG));
		$cfg = Config::initialize($cfgFile);

		$source = $input->getArgument(self::SOURCE_ARG);
		$sink = $input->getArgument(self::TARGET_ARG);

		if (!$cfg->hasSource($source)) {
			throw new InvalidArgumentException('Undefined source ' . $source);
		}
		if (!$cfg->hasTarget($sink)) {
			throw new InvalidArgumentException('Undefined sink ' . $sink);
		}
		$sourceData = $cfg->getSource($source);
		$sinkData = $cfg->getTarget($sink);

		[
			'class' => $sourceClass,
		] = $sourceData;
	}
}
