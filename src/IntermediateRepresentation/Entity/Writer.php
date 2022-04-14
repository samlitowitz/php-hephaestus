<?php

namespace PhpHephaestus\IntermediateRepresentation\Entity;

use PhpHephaestus\IntermediateRepresentation\EntityCollection;

interface Writer {
	public function write(EntityCollection $collection): void;
}
