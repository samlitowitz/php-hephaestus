<?php

namespace PhpHephaestus\IO\Entity\Reader\MySQL;

use PDO;
use PhpHephaestus\IntermediateRepresentation\Entity;
use PhpHephaestus\IntermediateRepresentation\EntityCollection;
use PhpHephaestus\IntermediateRepresentation\Property;
use PhpHephaestus\IntermediateRepresentation\PropertyCollection;
use PhpHephaestus\IntermediateRepresentation\Type\Scalar\Float_;
use PhpHephaestus\IntermediateRepresentation\Type\Scalar\Integer;
use PhpHephaestus\IntermediateRepresentation\UnknownType;
use PhpHephaestus\IO\Entity\Reader;

final class Table implements Reader {
	/** @var PDO */
	private $pdo;
	/** @var string */
	private $tableName;

	public function __construct(PDO $pdo, string $tableName)
	{
		$this->pdo = $pdo;
		$this->tableName = $tableName;
	}

	public function read(): EntityCollection
	{
		$stmt = $this->pdo->query('DESCRIBE ' . $this->tableName);
		$columnDefs = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$props = new PropertyCollection([]);
		foreach (
			$columnDefs as [
				'Field' => $colName,
				'Type' => $colType,
			]
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
