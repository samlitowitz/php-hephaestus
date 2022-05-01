<?php

namespace PhpHephaestus\App\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;

final class CLI extends Application
{
	public const CONFIG_ARG = 'config';

	protected function getDefaultInputDefinition(): InputDefinition
	{
		$definition = parent::getDefaultInputDefinition();
		$definition->addArgument(
			new InputArgument(
				self::CONFIG_ARG,
				InputArgument::REQUIRED,
				'Configuration file to use',
				'php-hephaestus.json'
			)
		);
		return $definition;
	}
}
