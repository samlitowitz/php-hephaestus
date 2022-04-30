<?php

namespace PhpHephaestus\IO\Entity;

use PhpHephaestus\IntermediateRepresentation\EntityCollection;

interface Reader
{
	public function read(): EntityCollection;
}
