<?php

namespace PhpHephaestus\PSR;

final class PSR1 {
	public function toCamelCase(string $s): string
	{
		switch (true) {
			case $this->isSnakeCase($s):
				return $this->snakeCaseToCamelCase($s);
			default:
				// TODO: exception
		}
	}

	public function toStudlyCaps(string $s): string
	{
		switch (true) {
			case $this->isSnakeCase($s):
				return $this->snakeCaseToStudlyCaps($s);
			default:
				// TODO: exception
		}
	}

	public function isSnakeCase(string $s): bool
	{
		return \preg_match('/^[a-zA-Z0-9]+(_[a-zA-Z0-9]+)*$/', $s) === 1;
	}

	public function snakeCaseToCamelCase(string $s): string
	{
		$s = \preg_replace('/[^a-zA-Z0-9]/', ' ', $s);
		$s = \preg_replace_callback('/\s([a-zA-Z])/', $this->toUpperFn(), $s);
		return \preg_replace('/[^a-zA-Z0-9]/', '', $s);
	}

	public function snakeCaseToStudlyCaps(string $s): string
	{
		$s = \preg_replace('/[^a-zA-Z0-9]/', ' ', $s);
		$s = \preg_replace_callback('/\s([a-zA-Z])/', $this->toUpperFn(), $s);
		return ucfirst(\preg_replace('/[^a-zA-Z0-9]/', '', $s));
	}

	private function toUpperFn(): callable
	{
		return function (array $matches): string {
			return strtoupper($matches[0]);
		};
	}
}
