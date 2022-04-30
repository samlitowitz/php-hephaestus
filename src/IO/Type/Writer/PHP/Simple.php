<?php

namespace PhpHephaestus\IO\Type\Writer\PHP;

use PhpHephaestus\IO\Type\Writer;
use PhpHephaestus\IntermediateRepresentation\Type;
use RuntimeException;

final class Simple implements Writer
{
	public function write(Type $t): string
	{
		switch (true) {
			case $t instanceof Type\Scalar\Binary:
			case $t instanceof Type\Scalar\Enumeration;
			case $t instanceof Type\Scalar\String_;
				return 'string';
			case $t instanceof Type\Scalar\Currency:
			case $t instanceof Type\Scalar\Integer;
				return 'int';
			case $t instanceof Type\Scalar\Date;
			case $t instanceof Type\Scalar\DateTime;
			case $t instanceof Type\Scalar\Time;
				return '\\DateTime';
			case $t instanceof Type\Scalar\Float_;
				return 'float';
			default:
				throw new RuntimeException('Unsupported type ' .\get_class($t));
		}
	}
}
