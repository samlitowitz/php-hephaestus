<?php

namespace PhpHephaestus\App\Console\Command;

use InvalidArgumentException;
use PhpHephaestus\App\Console\CLI;
use PhpHephaestus\App\Console\Config;
use PhpHephaestus\IO\Entity\Reader;
use PhpHephaestus\IO\Entity\Writer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Pipe extends Command
{
	protected static $defaultName = 'pipe';
	protected static $defaultDescription = 'Pipe sources to targets';
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
				'Target'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$configFile = \file_get_contents($input->getArgument(CLI::CONFIG_ARG));
		$config = Config::initialize($configFile);

		$sourceName = $input->getArgument(self::SOURCE_ARG);
		$targetName = $input->getArgument(self::TARGET_ARG);

		if (!$config->hasSource($sourceName)) {
			throw new InvalidArgumentException('Undefined source ' . $sourceName);
		}
		if (!$config->hasTarget($targetName)) {
			throw new InvalidArgumentException('Undefined sink ' . $targetName);
		}
		$sourceData = $config->getSource($sourceName);
		$targetData = $config->getTarget($targetName);

		[
			'class' => $sourceClass,
			'config' => $sourceConfig,
		] = $sourceData;
		if (!\class_exists($sourceClass)) {
			throw new InvalidArgumentException(
				sprintf(
					'source %s: class %s does not exist',
					$sourceName,
					$sourceClass
				)
			);
		}
		/** @var Reader $source */
		$source = \call_user_func_array([$sourceClass, 'configure'], $sourceConfig);

		[
			'class' => $targetClass,
			'config' => $targetConfig,
		] = $targetData;
		if (!\class_exists($targetClass)) {
			throw new InvalidArgumentException(
				sprintf(
					'target %s: class %s does not exist',
					$targetName,
					$targetClass
				)
			);
		}

		$entities = $source->read();
		foreach ($entities as $entity) {
			$targetConfig['className'] = $entity->getName();
			/** @var Writer $target */
			$target = \call_user_func_array([$targetClass, 'configure'], $targetConfig);
			$target->write($entity);
		}
	}
}
