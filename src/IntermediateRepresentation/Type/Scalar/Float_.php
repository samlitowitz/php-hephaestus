<?php

namespace PhpHephaestus\IntermediateRepresentation\Type\Scalar;

use PhpHephaestus\IntermediateRepresentation\Type\Scalar;

final class Float_ implements Scalar {
	public function jsonSerialize(): array
	{
		return [
			'name' => 'float',
		];
	}
}
