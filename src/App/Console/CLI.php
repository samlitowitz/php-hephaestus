<?php

namespace PhpHephaestus\App\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

final class CLI extends Application
{
	public const CONFIG_OPT = 'config';

	protected function getDefaultInputDefinition(): InputDefinition
	{
		$definition = parent::getDefaultInputDefinition();
		$definition->addOption(
			new InputOption(
				self::CONFIG_OPT,
				null,
				InputOption::VALUE_REQUIRED,
				'Configuration file to use',
				'php-hephaestus.json'
			)
		);
		return $definition;
	}
}
