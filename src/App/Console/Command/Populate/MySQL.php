<?php

namespace PhpHephaestus\App\Console\Command\Populate;

use PDO;
use PhpHephaestus\App\Tests\ScalarProps;
use PhpHephaestus\IO\Entity\MySQL\Table;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class MySQL extends Command
{
	protected static $defaultName = 'populate:mysql';
	protected static $defaultDescription = 'Populate MySQL database';
	private const MYSQL_HOST = 'host';
	private const MYSQL_DATABASE = 'database';
	private const MYSQL_PORT = 'port';
	private const MYSQL_USER = 'user';
	private const MYSQL_PASSWORD = 'password';

	protected function configure(): void
	{
		$this
			->addArgument(
				self::MYSQL_HOST,
				InputArgument::OPTIONAL
			)
			->addArgument(
				self::MYSQL_DATABASE,
				InputArgument::OPTIONAL
			)
			->addArgument(
				self::MYSQL_PORT,
				InputArgument::OPTIONAL
			)
			->addArgument(
				self::MYSQL_USER,
				InputArgument::OPTIONAL
			)
			->addArgument(
				self::MYSQL_PASSWORD,
				InputArgument::OPTIONAL
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$host = $input->getArgument(self::MYSQL_HOST);
		$database = $input->getArgument(self::MYSQL_DATABASE);
		$port = $input->getArgument(self::MYSQL_PORT);
		$user = $input->getArgument(self::MYSQL_USER);
		$password = $input->getArgument(self::MYSQL_PASSWORD);

		$pdo = new PDO(
			sprintf(
				'mysql:host=%s;port=%d;dbname=%s',
				$host,
				$port,
				$database
			),
			$user,
			$password
		);

		$pdo->query(
			"CREATE TABLE `integer_test` (
				`int` INT,
				`smallint` SMALLINT,
				`mediumint` MEDIUMINT,
				`bigint` BIGINT
			)"
		);

		$pdo->query(
			"CREATE TABLE `fixed_test` (
				`decimal_both` DECIMAL(2, 2),
				`decimal_one` DECIMAL(2),
				`numeric_both` NUMERIC(2, 2),
				`numeric_one` NUMERIC(2)
			)"
		);

		$pdo->query(
			"CREATE TABLE `float_test` (
				`float` FLOAT,
				`double_precision` DOUBLE PRECISION
			)"
		);

		return Command::SUCCESS;
	}
}