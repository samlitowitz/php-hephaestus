<?php

namespace PhpHephaestus\IntermediateRepresentation;

use Countable;
use Iterator;

final class PropertyCollection implements Countable, Iterator
{
	/** @var []Property */
	private $items = [];
	/** @var ?int */
	private $iter = null;

	public function __construct(?array $items)
	{
		if ($items === null) {
			return;
		}
		foreach ($items as $item) {
			$this->add($item);
		}
	}

	public function current(): ?Property
	{
		if ($this->iter === null) {
			return null;
		}
		if (!\array_key_exists($this->iter, $this->items)) {
			return null;
		}
		return $this->items[$this->iter];
	}

	public function next()
	{
		if (!$this->valid()) {
			return;
		}
		$this->iter++;
	}

	public function key(): ?int
	{
		return $this->iter;
	}

	public function valid(): bool
	{
		return $this->current() !== null;
	}

	public function rewind(): void
	{
		if ($this->count() === 0) {
			$this->iter = null;
			return;
		}
		$this->iter = 0;
	}

	public function count(): int
	{
		return count($this->items);
	}

	public function add(Property $property)
	{
		$this->items[] = $property;
	}
}
