<?php

namespace PhpHephaestus\IntermediateRepresentation\Constraint;

use PhpHephaestus\IntermediateRepresentation\Constraint;

final class MinLength implements Constraint {
	/** @var int */
	private $minLength;

	public function __construct(int $minLength)
	{
		$this->setMinLength($minLength);
	}

	public function getMinLength(): int
	{
		return $this->minLength;
	}

	public function setMinLength(int $minLength): void
	{
		$this->minLength = $minLength;
	}
}
