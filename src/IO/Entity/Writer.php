<?php

namespace PhpHephaestus\IO\Entity;

use PhpHephaestus\IntermediateRepresentation\Entity;

interface Writer
{
	public function write(Entity $entity): void;
}
