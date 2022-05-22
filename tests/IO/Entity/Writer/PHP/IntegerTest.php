<?php

namespace PhpHephaestus\Tests\IO\Entity\Writer\PHP;

final class IntegerTest
{
	/** @var ?int $int */
	private $int;
	/** @var ?int $smallint */
	private $smallint;
	/** @var ?int $mediumint */
	private $mediumint;
	/** @var ?int $bigint */
	private $bigint;

	public function getInt(): ?int
	{
		return $this->int;
	}

	public function setInt(?int $int): void
	{
		$this->int = $int;
	}

	public function getSmallint(): ?int
	{
		return $this->smallint;
	}

	public function setSmallint(?int $smallint): void
	{
		$this->smallint = $smallint;
	}

	public function getMediumint(): ?int
	{
		return $this->mediumint;
	}

	public function setMediumint(?int $mediumint): void
	{
		$this->mediumint = $mediumint;
	}

	public function getBigint(): ?int
	{
		return $this->bigint;
	}

	public function setBigint(?int $bigint): void
	{
		$this->bigint = $bigint;
	}
}
