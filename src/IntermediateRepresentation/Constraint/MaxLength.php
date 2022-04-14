<?php

namespace PhpHephaestus\IntermediateRepresentation\Constraint;

use PhpHephaestus\IntermediateRepresentation\Constraint;

final class MaxLength implements Constraint {
	/** @var int */
	private $maxLength;

	public function __construct(int $maxLength)
	{
		$this->setMaxLength($maxLength);
	}

	public function getMaxLength(): int
	{
		return $this->maxLength;
	}

	public function setMaxLength(int $maxLength): void
	{
		$this->maxLength = $maxLength;
	}
}
