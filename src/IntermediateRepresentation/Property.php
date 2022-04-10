<?php

namespace PhpHephaestus\IntermediateRepresentation;

final class Property {
	/** @var string */
	private $name;
	/** @var Type */
	private $type;

	public function __construct(string $name, Type $type)
	{
		$this->name = $name;
		$this->type = $type;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function getType(): Type
	{
		return $this->type;
	}

	public function setType(Type $type): void
	{
		$this->type = $type;
	}
}
