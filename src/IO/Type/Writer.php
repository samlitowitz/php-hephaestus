<?php

namespace PhpHephaestus\IO\Type;

use PhpHephaestus\IntermediateRepresentation\Type;

interface Writer {
	public function write(Type $t): string;
}
