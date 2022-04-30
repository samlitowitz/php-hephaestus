<?php

namespace PhpHephaestus\OS;

use InvalidArgumentException;
use PhpHephaestus\IO\Closer;
use PhpHephaestus\IO\Writer;
use RuntimeException;

final class File implements Closer, Writer {
	/** @var resource $h */
	private $h;

	public static function openFile(string $filename, string $mode, bool $use_include_path = false, $context = null): File
	{
		$f = new File();
		$h = fopen($filename, $mode, $use_include_path, $context);
		if ($h === false) {
			throw new RuntimeException(
				sprintf(
					'Failed to open %s with mode %s',
					$filename,
					$mode
				)
			);
		}
		$f->h = $h;
		return $f;
	}

	public function close(): void
	{
		if ($this->h === null) {
			return;
		}
		\fclose($this->h);
	}

	public function write(string $d): int
	{
		if ($this->h === null) {
			throw new RuntimeException('Write failed: invalid resource');
		}
		$n = fwrite($this->h, $d);
		if ($n === false) {
			throw new RuntimeException('Write failed: unknown cause');
		}
		if ($n !== strlen($d)) {
			throw new RuntimeException('Write failed: incomplete write');
		}
		return $n;
	}
}
