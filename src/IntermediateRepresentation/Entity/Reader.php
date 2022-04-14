<?php

namespace PhpHephaestus\IntermediateRepresentation\Entity;

use PhpHephaestus\IntermediateRepresentation\EntityCollection;

interface Reader
{
	public function read(): EntityCollection;
}
