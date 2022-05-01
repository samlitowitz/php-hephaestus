<?php

namespace PhpHephaestus\App\Console;

final class Config
{
	private array $sources;
	private array $sinks;

	private function __construct(array $sources, array $sinks)
	{
		$this->sources = $sources;
		$this->sinks = $sinks;
	}

	public static function initialize(string $json): Config
	{
		$raw = \json_decode($json, true);
		[
			'sources' => $sources,
			'sinks' => $sinks,
		] = $raw;
		return new Config($sources, $sinks);
	}

	public function hasSource(string $source): bool
	{
		return \array_key_exists($source, $this->sources);
	}

	public function hasSink(string $sink): bool
	{
		return \array_key_exists($sink, $this->sinks);
	}

	public function getSource(string $source): ?array
	{
		if (!$this->hasSource($source)) {
			return null;
		}
		return $this->sources[$source];
	}

	public function getSink(string $sink): ?array
	{
		if (!$this->hasSink($sink)) {
			return null;
		}
		return $this->sinks[$sink];
	}
}
