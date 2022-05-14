<?php

namespace PhpHephaestus\IO\Entity\PHP;

use InvalidArgumentException;
use PhpHephaestus\App\Console\Configurable;
use PhpHephaestus\IntermediateRepresentation\Entity;
use PhpHephaestus\IO\Entity\Writer;
use PhpHephaestus\IO\Type\PHP\Simple;
use PhpHephaestus\PSR\PSR1;
use PhpParser\Comment\Doc;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\Node\Stmt\Return_;
use PhpParser\PrettyPrinter\Standard;
use RuntimeException;

final class SimpleClass implements Configurable, Writer
{
	/** @var \PhpHephaestus\IO\Writer */
	private $w;
	/** @var \PhpHephaestus\IO\Type\Writer */
	private $tw;
	/** @var string $namespace */
	private $namespace;

	public function __construct(
		\PhpHephaestus\IO\Writer $w,
		\PhpHephaestus\IO\Type\Writer $tw,
		string $namespace
	) {
		$this->w = $w;
		$this->tw = $tw;
		$this->namespace = $namespace;
	}

	public static function configure(array $config): self
	{
		if (!\array_key_exists('outputDirectory', $config)) {
			throw new InvalidArgumentException(
				sprintf(
					'%s: missing required configuration option `outputDirectory`',
					__CLASS__
				)
			);
		}

		if (!\array_key_exists('namespace', $config)) {
			throw new InvalidArgumentException(
				sprintf(
					'%s: missing required configuration option `namespace`',
					__CLASS__
				)
			);
		}

		if (!\array_key_exists('className', $config)) {
			throw new InvalidArgumentException(
				sprintf(
					'%s: missing required configuration option `className`',
					__CLASS__
				)
			);
		}

		[
			'outputDirectory' => $outputDirectory,
			'namespace' => $namespace,
			'className' => $className,
		] = $config;


		$psr1 = new PSR1();
		$f = File::openFile(
			sprintf('%s/%s.php', $outputDirectory, $psr1->snakeCaseToStudlyCaps($className)),
			'w'
		);
		return new self($f, new Simple(), $namespace);
	}

	public function write(Entity $entity): void
	{
		$psr1 = new PSR1();
		$propertyStmts = [];
		$fnStmts = [];

		$properties = $entity->getProperties();
		foreach ($properties as $property) {
			$camelCase = $psr1->toCamelCase($property->getName());
			$studlyCase = $psr1->toStudlyCaps($property->getName());
			$typ = '?' . $this->tw->write($property->getType());

			$propertyStmt = new Property(
				Class_::MODIFIER_PRIVATE,
				[
					new PropertyProperty($camelCase)
				]
			);

			$propertyStmt->setDocComment(
				new Doc(
					sprintf(
						'/** @var %s $%s */',
						$typ,
						$property->getName()
					)
				)
			);
			$propertyStmts[] = $propertyStmt;

			$getFnStmt = new ClassMethod(
				sprintf('get%s', $studlyCase),
				[
					'flags' => Class_::MODIFIER_PUBLIC,
					'returnType' => $typ,
					'stmts' => [
						new Return_(
							new PropertyFetch(
								new Variable('this'),
								$camelCase
							)
						)
					]
				]
			);
			$fnStmts[] = $getFnStmt;

			$setFnStmt = new ClassMethod(
				sprintf('set%s', $studlyCase),
				[
					'flags' => Class_::MODIFIER_PUBLIC,
					'params' => [
						new Param(
							new Variable($camelCase),
							null,
							$typ
						)
					],
					'returnType' => 'void',
					'stmts' => [
						new Expression(
							new Assign(
								new PropertyFetch(
									new Variable('this'),
									$camelCase
								),
								new Variable($camelCase)
							)
						)
					]
				]
			);
			$fnStmts[] = $setFnStmt;
		}

		$class = new Class_(
			$psr1->toStudlyCaps($entity->getName()),
			[
				'flags' => Class_::MODIFIER_FINAL,
				'stmts' => \array_merge($propertyStmts, $fnStmts),
			]
		);

		$namespace = new Namespace_(
			new Name($this->namespace),
			[$class]
		);


		$prettyPrinter = new Standard();
		$code = $prettyPrinter->prettyPrintFile([$namespace]);
		$n = $this->w->write($code);
		if ($n !== strlen($code)) {
			throw new RuntimeException('Write failed: incomplete write');
		}
	}
}
