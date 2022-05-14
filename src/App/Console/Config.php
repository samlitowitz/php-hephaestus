<?php

namespace PhpHephaestus\App\Console;

final class Config
{
	private array $sources;
	private array $targets;

	private function __construct(array $sources, array $targets)
	{
		$this->sources = $sources;
		$this->targets = $targets;
	}

	public static function initialize(string $json): Config
	{
		$raw = \json_decode($json, true);
		[
			'sources' => $sources,
			'targets' => $targets,
		] = $raw;
		return new Config($sources, $targets);
	}

	public function hasSource(string $source): bool
	{
		return \array_key_exists($source, $this->sources);
	}

	public function hasTarget(string $target): bool
	{
		return \array_key_exists($target, $this->targets);
	}

	public function getSource(string $source): ?array
	{
		if (!$this->hasSource($source)) {
			return null;
		}
		return $this->sources[$source];
	}

	public function getTarget(string $target): ?array
	{
		if (!$this->hasTarget($target)) {
			return null;
		}
		return $this->targets[$target];
	}
}
