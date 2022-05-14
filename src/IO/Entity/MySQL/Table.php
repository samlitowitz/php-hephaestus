<?php

namespace PhpHephaestus\IO\Entity\MySQL;

use InvalidArgumentException;
use PDO;
use PhpHephaestus\App\Console\Configurable;
use PhpHephaestus\IntermediateRepresentation\Entity;
use PhpHephaestus\IntermediateRepresentation\EntityCollection;
use PhpHephaestus\IntermediateRepresentation\Property;
use PhpHephaestus\IntermediateRepresentation\PropertyCollection;
use PhpHephaestus\IntermediateRepresentation\Type\Scalar\Float_;
use PhpHephaestus\IntermediateRepresentation\Type\Scalar\Integer;
use PhpHephaestus\IntermediateRepresentation\UnknownType;
use PhpHephaestus\IO\Entity\Reader;

final class Table implements Configurable, Reader
{
	/** @var PDO */
	private $pdo;
	/** @var string */
	private $tableName;

	public function __construct(PDO $pdo, string $tableName)
	{
		$this->pdo = $pdo;
		$this->tableName = $tableName;
	}

	public static function configure(array $config): self
	{
		if (!\array_key_exists('connection', $config)) {
			throw new InvalidArgumentException(
				sprintf(
					'%s: missing required configuration option `connection`',
					__CLASS__
				)
			);
		}

		if (!\array_key_exists('table', $config)) {
			throw new InvalidArgumentException(
				sprintf(
					'%s: missing required configuration option `table`',
					__CLASS__
				)
			);
		}

		[
			'connection' => $connection,
			'table' => $table,
		] = $config;

		foreach (['host', 'database', 'port', 'user', 'password'] as $option) {
			if (!\array_key_exists($option, $connection)) {
				throw new InvalidArgumentException(
					sprintf(
						'%s: missing required `connection` option `%s`',
						__CLASS__,
						$option
					)
				);
			}
		}

		[
			'host' => $host,
			'database' => $database,
			'port' => $port,
			'user' => $user,
			'password' => $password,
		] = $connection;

		return new self(
			new PDO(
				sprintf(
					'mysql:host=%s;dbname=%s;port=%d',
					$host,
					$database,
					$port
				),
				$user,
				$password
			),
			$table
		);
	}

	public function read(): EntityCollection
	{
		$stmt = $this->pdo->query('DESCRIBE ' . $this->tableName);
		$columnDefs = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$props = new PropertyCollection([]);
		foreach (
			$columnDefs as ['Field' => $colName,
			'Type' => $colType,]
		) {
			switch (true) {
				case \preg_match('/^INTEGER\([0-9]+\)/i', $colType):
				case \preg_match('/^INT\([0-9]+\)/i', $colType):
				case \preg_match('/^SMALLINT\([0-9]+\)/i', $colType):
				case \preg_match('/^MEDIUMINT\([0-9]+\)/i', $colType):
				case \preg_match('/^BIGINT\([0-9]+\)/i', $colType):
					$props->add(new Property($colName, new Integer()));
					break;
				case \preg_match('/^DECIMAL\([0-9]+(,\s*[0-9]+)?\)/i', $colType):
				case \preg_match('/^NUMERIC\([0-9]+(,\s*[0-9]+)?\)/i', $colType):
				case \preg_match('/^FLOAT/i', $colType):
				case \preg_match('/^DOUBLE(\sPRECISION)?/i', $colType):
					$props->add(new Property($colName, new Float_()));
					break;
				default:
					$props->add(new Property($colName, new UnknownType()));
			}
		}

		return new EntityCollection([
			new Entity($this->tableName, $props),
		]);
	}
}
