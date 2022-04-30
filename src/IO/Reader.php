<?php

namespace PhpHephaestus\IO;

interface Reader {
	/**
	 * Read up to $n bytes.
	 * If no bytes can be read throw a PhpHephaestus\IO\EndOfFileException.
	 */
	public function read(int $n): string;
}
