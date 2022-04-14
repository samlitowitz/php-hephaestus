<?php

namespace PhpHephaestus\IntermediateRepresentation\Type\Scalar;

use PhpHephaestus\IntermediateRepresentation\Type\Scalar;

final class FloatType implements Scalar {
	public function jsonSerialize(): array
	{
		return [
			'name' => 'float',
		];
	}
}
