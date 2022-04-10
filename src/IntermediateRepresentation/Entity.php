<?php

namespace PhpHephaestus\IntermediateRepresentation;

final class Entity {
	/** @var string */
	private $name;
	/** @var PropertyCollection */
	private $properties;

	public function __construct(string $name, PropertyCollection $properties = null)
	{
		$this->setName($name);
		if ($properties === null) {
			$properties = new PropertyCollection([]);
		}
		$this->setProperties($properties);
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function getProperties(): PropertyCollection
	{
		return $this->properties;
	}

	public function setProperties(PropertyCollection $properties): void
	{
		$this->properties = $properties;
	}
}
