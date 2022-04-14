<?php

namespace PhpHephaestus\IntermediateRepresentation;

final class UnknownType implements Type {
	public function jsonSerialize()
	{
		return [
			'name' => 'unknown',
		];
	}
}
