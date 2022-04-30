<?php

namespace PhpHephaestus\App\Tests;

use PhpHephaestus\IntermediateRepresentation\Entity;
use PhpHephaestus\IntermediateRepresentation\Property;
use PhpHephaestus\IntermediateRepresentation\PropertyCollection;
use PhpHephaestus\IntermediateRepresentation\Type\Scalar\Binary;
use PhpHephaestus\IntermediateRepresentation\Type\Scalar\Currency;
use PhpHephaestus\IntermediateRepresentation\Type\Scalar\Date;
use PhpHephaestus\IntermediateRepresentation\Type\Scalar\DateTime;
use PhpHephaestus\IntermediateRepresentation\Type\Scalar\Enumeration;
use PhpHephaestus\IntermediateRepresentation\Type\Scalar\Float_;
use PhpHephaestus\IntermediateRepresentation\Type\Scalar\Integer;
use PhpHephaestus\IntermediateRepresentation\Type\Scalar\String_;
use PhpHephaestus\IntermediateRepresentation\Type\Scalar\Time;
use PhpHephaestus\IO\Entity\Writer\PHP\SimpleClass;
use PhpHephaestus\IO\Entity\Writer\PHP\SimplePHPUnit;
use PhpHephaestus\IO\Type\Writer\PHP\Simple;
use PhpHephaestus\OS\File;
use PhpHephaestus\PSR\PSR1;

final class ScalarProps
{
	public static function generate(string $baseDir, string $namespace): void
	{
		self::generateScalarPropUsingEntityWriterPHPSimpleClass(
			$baseDir . '/IO/Entity/Writer/PHP',
			$namespace . 'IO\\Entity\\Writer\\PHP'
		);
		self::generateScalarPropUsingEntityWriterPHPSimplePHPUnit(
			$baseDir . '/IO/Entity/Writer/PHP',
			$namespace . 'IO\\Entity\\Writer\\PHP'
		);
	}

	private static function generateScalarPropUsingEntityWriterPHPSimpleClass(string $baseDir, string $namespace): void
	{
		$psr1 = new PSR1();
		$entity = self::scalarPropsEntity();
		$f = File::openFile(
			sprintf('%s/%s.php', $baseDir, $psr1->snakeCaseToStudlyCaps($entity->getName())),
			'w'
		);

		try {
			$w = new SimpleClass($f, new Simple(), $namespace);
			$w->write($entity);
		} finally {
			$f->close();
		}
	}

	private static function generateScalarPropUsingEntityWriterPHPSimplePHPUnit(string $baseDir, string $namespace): void
	{
		$psr1 = new PSR1();
		$entity = self::scalarPropsEntity();
		$f = File::openFile(
			sprintf('%s/%sTest.php', $baseDir, $psr1->snakeCaseToStudlyCaps($entity->getName())),
			'w'
		);

		try {
			$w = new SimplePHPUnit($f, new Simple(), $namespace);
			$w->write($entity);
		} finally {
			$f->close();
		}
	}

	private static function scalarPropsEntity(): Entity
	{
		return new Entity(
			'php_simple_class_scalar_props',
			new PropertyCollection(
				[
					new Property('prop_binary', new Binary()),
					new Property('prop_currency', new Currency()),
					new Property('prop_date', new Date()),
					new Property('prop_datetime', new DateTime()),
					new Property('prop_enumeration', new Enumeration()),
					new Property('prop_float', new Float_()),
					new Property('prop_integer', new Integer()),
					new Property('prop_string', new String_()),
					new Property('prop_time', new Time()),
				]
			)
		);
	}
}
