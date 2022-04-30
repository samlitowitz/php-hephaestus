<?php

namespace PhpHephaestus\Tests\IntermediateRepresentation\Entity\Writer\PHP;

use DateInterval;
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
use PhpHephaestus\IO\Entity\Writer\PHP\Simple;
use PhpHephaestus\OS\File;
use PHPUnit\Framework\TestCase;

final class SimpleTest extends TestCase
{
	/** @var string[] $filesToDelete */
	private static $filesToDelete;

	public static function setUpBeforeClass(): void
	{
		self::$filesToDelete = [];
		self::setupScalarPropsEntity();
	}

	public static function tearDownAfterClass(): void
	{
		foreach (self::$filesToDelete as $file) {
			self::assertTrue(\unlink($file));
		}
	}

	/**
	 * @dataProvider scalar_props_entity_getters_and_setters_provider
	 */
	public function test_scalar_props_entity_getters_and_setters(
		string $className,
		string $fnSuffix,
		callable $compareFn,
		$expected
	): void {
		$obj = new $className();
		\call_user_func([$obj, 'set' . $fnSuffix], $expected);
		$result = \call_user_func([$obj, 'get' . $fnSuffix]);
		$this->assertTrue($compareFn($expected, $result));
	}

	public function scalar_props_entity_getters_and_setters_provider()
	{
		$entity = self::scalarPropsEntity();
		$properties = $entity->getProperties();
		$testCases = [];

		foreach ($properties as $property) {
			$t = $property->getType();
			$compareFn = function () {
				return false;
			};
			$expected = null;
			switch (true) {
				case $t instanceof Binary:
				case $t instanceof Enumeration;
				case $t instanceof String_;
					$compareFn = function (string $a, string $b) {
						return strcmp($a, $b) === 0;
					};
					$expected = \uniqid();
					break;
				case $t instanceof Currency:
				case $t instanceof Integer;
					$compareFn = function (int $a, int $b) {
						return $a === $b;
					};
					$expected = \random_int(0, 1000);
					break;
				case $t instanceof Date;
				case $t instanceof DateTime;
				case $t instanceof Time;
					$compareFn = function (\DateTime $a, \DateTime $b) {
						return $a->getTimestamp() === $b->getTimestamp();
					};
					$expected = (new \DateTime())->add(
						new DateInterval(
							sprintf(
								'P%dD',
								\random_int(0, 9)
							)
						)
					);
					break;
				case $t instanceof Float_;
					$compareFn = function (float $a, float $b) {
						return abs($a - $b) < 0.1;
					};
					$expected = \rand(0, 10);
					break;
				default:
					continue 2;
			}

			$testCases[$property->getName()] = [
				// $className
				__NAMESPACE__ . '\\' . self::snakeCaseToStudlyCaps($entity->getName()),
				// $fnSuffix
				self::snakeCaseToCamelCase($property->getName()),
				// $compareFn,
				$compareFn,
				// $expected,
				$expected,
			];
		}
		return $testCases;
	}

	private static function setupScalarPropsEntity(): void
	{
		$entity = self::scalarPropsEntity();
		$f = File::openFile(
			sprintf('%s/%s.php', __DIR__, self::snakeCaseToStudlyCaps($entity->getName())),
			'w'
		);

		try {
			$w = new Simple($f, new \PhpHephaestus\IO\Type\Writer\PHP\Simple(), __NAMESPACE__);
			$w->write($entity);
		} finally {
			$f->close();
			self::$filesToDelete[] = $f->getName();
		}
	}

	private static function scalarPropsEntity(): Entity
	{
		return new Entity(
			'scalar_props_entity',
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

	private static function snakeCaseToCamelCase(string $s): string
	{
		$s = \preg_replace('/[^a-zA-Z0-9]/', ' ', $s);
		$s = \preg_replace_callback('/\s([a-zA-Z])/', self::toUpperFn(), $s);
		return \preg_replace('/[^a-zA-Z0-9]/', '', $s);
	}

	private static function snakeCaseToStudlyCaps(string $s): string
	{
		$s = \preg_replace('/[^a-zA-Z0-9]/', ' ', $s);
		$s = \preg_replace_callback('/\s([a-zA-Z])/', self::toUpperFn(), $s);
		return ucfirst(\preg_replace('/[^a-zA-Z0-9]/', '', $s));
	}

	private static function toUpperFn(): callable
	{
		return function (array $matches): string {
			return strtoupper($matches[0]);
		};
	}
}
