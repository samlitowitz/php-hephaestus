<?php

namespace PhpHephaestus\Bytes;

use InvalidArgumentException;
use PhpHephaestus\IO\EndOfFileException;
use PhpHephaestus\IO\Reader;
use PhpHephaestus\IO\Writer;

final class Buffer implements Reader, Writer {
	/** @var string $buf */
	private $buf = '';
	/** @var int $i */
	private $i = 0;

	public function __construct(?string $buf = null)
	{
		$this->write($buf ?? '');
	}

	public function len(): int
	{
		return strlen($this->buf);
	}

	public function read(int $n): string
	{
		if ($n < 0) {
			throw new InvalidArgumentException('cannot read less than 1 byte');
		}
		if ($n === 0) {
			return '';
		}
		if ($this->i >= $this->len()) {
			throw new EndOfFileException();
		}
		$n = min($n, $this->len() - $this->i);

		$s = substr($this->buf, $this->i, $n);
		$this->i += $n;
		return $s;
	}

	public function reset(): void
	{
		$this->i = 0;
	}

	public function write(string $d): int
	{
		$this->buf .= $d;
		return strlen($d);
	}
}
