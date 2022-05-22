<?php

namespace PhpHephaestus\Tests\IO\Entity\Writer\PHP;

use PHPUnit\Framework\TestCase;

final class IntegerTestTest extends TestCase
{
	/**
	 * @dataProvider accessorsAndMutatorsProvider
	 */
	public function testAccessorsAndMutators(string $fnSuffix, callable $compareFn, $expected): void
	{
		$obj = new IntegerTest();
		\call_user_func([$obj, 'set' . $fnSuffix], $expected);
		$result = \call_user_func([$obj, 'get' . $fnSuffix]);
		$this->assertTrue($compareFn($expected, $result));
	}

	public function accessorsAndMutatorsProvider(): array
	{
		return [
			'int' => [
				'Int',
				function (int $a, int $b): bool {
					return $a === $b;
				},
				\random_int(-1000, 1000)
			],
			'smallint' => [
				'Smallint',
				function (int $a, int $b): bool {
					return $a === $b;
				},
				\random_int(-1000, 1000)
			],
			'mediumint' => [
				'Mediumint',
				function (int $a, int $b): bool {
					return $a === $b;
				},
				\random_int(-1000, 1000)
			],
			'bigint' => [
				'Bigint',
				function (int $a, int $b): bool {
					return $a === $b;
				},
				\random_int(-1000, 1000)
			]
		];
	}
}
