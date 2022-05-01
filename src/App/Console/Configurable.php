<?php

namespace PhpHephaestus\App\Console;

interface Configurable
{
	public function configure(array $config): void;
}
