<?php

namespace PhpHephaestus\Tests\IO\Entity\Writer\PHP;

use PHPUnit\Framework\TestCase;

final class PhpSimpleClassScalarPropsTest extends TestCase
{
	/**
	 * @dataProvider accessorsAndMutatorsProvider
	 */
	public function testAccessorsAndMutators(string $fnSuffix, callable $compareFn, $expected): void
	{
		$obj = new PhpSimpleClassScalarProps();
		\call_user_func([$obj, 'set' . $fnSuffix], $expected);
		$result = \call_user_func([$obj, 'get' . $fnSuffix]);
		$this->assertTrue($compareFn($expected, $result));
	}

	public function accessorsAndMutatorsProvider(): array
	{
		return [
			'prop_binary' => [
				'PropBinary',
				function (string $a, string $b): bool {
					return \strcmp($a, $b) === 0;
				},
				\uniqid()
			],
			'prop_currency' => [
				'PropCurrency',
				function (int $a, int $b): bool {
					return $a === $b;
				},
				\random_int(-1000, 1000)
			],
			'prop_date' => [
				'PropDate',
				function (\DateTime $a, \DateTime $b): bool {
					return $a->getTimestamp() === $b->getTimestamp();
				},
				(new \DateTime())->add(new \DateInterval(\sprintf('P%dD', \random_int(0, 9))))
			],
			'prop_datetime' => [
				'PropDatetime',
				function (\DateTime $a, \DateTime $b): bool {
					return $a->getTimestamp() === $b->getTimestamp();
				},
				(new \DateTime())->add(new \DateInterval(\sprintf('P%dD', \random_int(0, 9))))
			],
			'prop_enumeration' => [
				'PropEnumeration',
				function (string $a, string $b): bool {
					return \strcmp($a, $b) === 0;
				},
				\uniqid()
			],
			'prop_float' => [
				'PropFloat',
				function (float $a, float $b): bool {
					return $a === $b;
				},
				\rand(-10.0, 10.0)
			],
			'prop_integer' => [
				'PropInteger',
				function (int $a, int $b): bool {
					return $a === $b;
				},
				\random_int(-1000, 1000)
			],
			'prop_string' => [
				'PropString',
				function (string $a, string $b): bool {
					return \strcmp($a, $b) === 0;
				},
				\uniqid()
			],
			'prop_time' => [
				'PropTime',
				function (\DateTime $a, \DateTime $b): bool {
					return $a->getTimestamp() === $b->getTimestamp();
				},
				(new \DateTime())->add(new \DateInterval(\sprintf('P%dD', \random_int(0, 9))))
			]
		];
	}
}
