<?php

namespace PhpHephaestus\IntermediateRepresentation;

use ArrayAccess;
use Countable;
use InvalidArgumentException;
use Iterator;
use JsonSerializable;

final class PropertyCollection implements Countable, Iterator, JsonSerializable
{
	/** @var []Property */
	private $items = [];
	/** @var ?int */
	private $iter = null;

	public function __construct(?array $items = null)
	{
		if ($items === null) {
			return;
		}
		foreach ($items as $item) {
			$this->add($item);
		}
	}

	public function add(Property $property)
	{
		$this->items[] = $property;
	}

	public function rewind(): void
	{
		if ($this->count() === 0) {
			$this->iter = null;
			return;
		}
		$this->iter = 0;
	}

	public function toArray(): array
	{
		return $this->items;
	}

	public function map(callable $callback): self
	{
		$copy = new self();
		foreach ($this as $property) {
			$copy->add(\call_user_func($callback, $property));
		}
		return $copy;
	}

	// -- Iterator --
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

	// -- Countable --
	public function count(): int
	{
		return count($this->items);
	}

	// -- JsonSerializable --
	public function jsonSerialize(): array
	{
		$json = [];
		/** @var Property $item */
		foreach ($this->items as $item) {
			$json[] = $item->jsonSerialize();
		}
		return $json;
	}
}
