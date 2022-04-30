<?php

namespace PhpHephaestus\IntermediateRepresentation\Type\Scalar;

use PhpHephaestus\IntermediateRepresentation\Type\Scalar;

final class String_ implements Scalar {
	public function jsonSerialize(): array
	{
		return [
			'name' => 'string',
		];
	}
}
