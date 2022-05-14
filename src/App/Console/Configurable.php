<?php

namespace PhpHephaestus\App\Console;

interface Configurable
{
	public static function configure(array $config): self;
}
