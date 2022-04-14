<?php

namespace PhpHephaestus\IntermediateRepresentation\Entity\Writer\PHP;

use PhpHephaestus\IntermediateRepresentation\EntityCollection;
use PhpHephaestus\IntermediateRepresentation\Entity\Writer;

final class SimpleModel implements Writer {
	public function write(EntityCollection $collection): void
	{
		// need to know where to write (resource, file_put_contents, ???)
		// need namespace
		// build AST: https://github.com/nikic/PHP-Parser
	}
}
