<?php

namespace PhpHephaestus\IntermediateRepresentation\Type\Scalar;

use PhpHephaestus\IntermediateRepresentation\Type\Scalar;

final class Enumeration implements Scalar {
	public function jsonSerialize(): array
	{
		return [
			'name' => 'enumeration',
		];
	}
}
