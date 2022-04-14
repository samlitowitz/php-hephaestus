<?php

namespace PhpHephaestus\IntermediateRepresentation\Type\Scalar;

use PhpHephaestus\IntermediateRepresentation\Type\Scalar;

final class Integer implements Scalar {
	public function jsonSerialize(): array
	{
		return [
			'name' => 'integer',
		];
	}
}
