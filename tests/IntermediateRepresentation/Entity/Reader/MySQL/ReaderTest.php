<?php

namespace PhpHephaestus\Tests\IntermediateRepresentation\Entity\Reader\MySQL;

use PDO;
use PhpHephaestus\IntermediateRepresentation\Property;
use PhpHephaestus\IntermediateRepresentation\PropertyCollection;
use PhpHephaestus\IntermediateRepresentation\Type\Scalar\Float_;
use PhpHephaestus\IntermediateRepresentation\Type\Scalar\Integer;
use PhpHephaestus\IntermediateRepresentation\Entity\Reader\MySQL\TableReader;
use PHPUnit\Framework\TestCase;

use RuntimeException;

use const MYSQL_DATABASE;
use const MYSQL_HOST;
use const MYSQL_PASSWORD;
use const MYSQL_PORT;
use const MYSQL_USER;

final class ReaderTest extends TestCase
{
	/** @var ?PDO */
	private static $pdo = null;

	public static function setUpBeforeClass(): void
	{
		self::$pdo = new PDO(
			sprintf(
				'mysql:host=%s;dbname=%s;port=%d',
				MYSQL_HOST,
				MYSQL_DATABASE,
				MYSQL_PORT
			),
			MYSQL_USER,
			MYSQL_PASSWORD
		);
	}

	public static function tearDownAfterClass(): void
	{
		self::$pdo = null;
	}

	public function tearDown(): void
	{
		$this->dropAllTables();
	}

	private function dropAllTables()
	{
		$stmt = self::$pdo->prepare('SELECT `table_name` FROM `information_schema`.`tables` WHERE `table_schema` = :schema');
		$stmt->bindValue(':schema', MYSQL_DATABASE);
		$stmt->execute();
		$table_names = $stmt->fetchAll(PDO::FETCH_COLUMN);
		if ($table_names === false) {
			throw new RuntimeException('Failed to fetch all tables');
		}
		$stmt = null;

		foreach ($table_names as $table_name) {
			self::$pdo->query(
				sprintf(
					'DROP TABLE IF EXISTS `%s`',
					$table_name
				)
			);
		}
	}

	public function test_read_table_with_integer_types_and_no_constraints(): void
	{
		$tableName = 'integer_test';
		$createTableQuery = sprintf(
<<<'SQL'
CREATE TABLE `%s` (
	`int` INT,
	`smallint` SMALLINT,
	`mediumint` MEDIUMINT,
	`bigint` BIGINT
);
SQL,
			$tableName
		);
		$expectedProps = new PropertyCollection([
			new Property('int', new Integer()),
			new Property('smallint', new Integer()),
			new Property('mediumint', new Integer()),
			new Property('bigint', new Integer()),
		]);
		self::$pdo->query($createTableQuery);

		$r = new TableReader(self::$pdo, $tableName);
		$entities = $r->read();

		$this->assertEquals(1, $entities->count());

		foreach ($entities as $entity) {
			$this->assertEquals($tableName, $entity->getName());
			$this->assertEqualsCanonicalizing(
				$expectedProps->jsonSerialize(),
				$entity->getProperties()->jsonSerialize()
			);
		}
	}

	public function test_read_table_with_fixed_types_and_no_constraints(): void
	{
		$tableName = 'fixed_test';
		$createTableQuery = sprintf(
			<<<'SQL'
CREATE TABLE `%s` (
	`decimal_both` DECIMAL(2, 2),
	`decimal_one` DECIMAL(2),
	`numeric_both` NUMERIC(2, 2),
	`numeric_one` NUMERIC(2)
);
SQL,
			$tableName
		);
		$expectedProps = new PropertyCollection([
			new Property('decimal_both', new Float_()),
			new Property('decimal_one', new Float_()),
			new Property('numeric_both', new Float_()),
			new Property('numeric_one', new Float_()),
		]);
		self::$pdo->query($createTableQuery);

		$r = new TableReader(self::$pdo, $tableName);
		$entities = $r->read();

		$this->assertEquals(1, $entities->count());

		foreach ($entities as $entity) {
			$this->assertEquals($tableName, $entity->getName());
			$this->assertEqualsCanonicalizing(
				$expectedProps->jsonSerialize(),
				$entity->getProperties()->jsonSerialize()
			);
		}
	}

	public function test_read_table_with_float_types_and_no_constraints(): void
	{
		$tableName = 'float_test';
		$createTableQuery = sprintf(
			<<<'SQL'
CREATE TABLE `%s` (
	`float` FLOAT,
	`double_precision` DOUBLE PRECISION
);
SQL,
			$tableName
		);
		$expectedProps = new PropertyCollection([
			new Property('float', new Float_()),
			new Property('double_precision', new Float_()),
		]);
		self::$pdo->query($createTableQuery);

		$r = new TableReader(self::$pdo, $tableName);
		$entities = $r->read();

		$this->assertEquals(1, $entities->count());

		foreach ($entities as $entity) {
			$this->assertEquals($tableName, $entity->getName());
			$this->assertEqualsCanonicalizing(
				$expectedProps->jsonSerialize(),
				$entity->getProperties()->jsonSerialize()
			);
		}
	}
}
