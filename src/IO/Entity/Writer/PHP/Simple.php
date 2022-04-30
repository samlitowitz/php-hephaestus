<?php

namespace PhpHephaestus\IO\Entity\Writer\PHP;

use PhpHephaestus\IntermediateRepresentation\Entity;
use PhpHephaestus\IO\Entity\Transformer\PHP\PSR1;
use PhpHephaestus\IO\Entity\Writer;
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

final class Simple implements Writer
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

	public function write(Entity $entity): void
	{
		$propertyStmts = [];
		$fnStmts = [];

		$properties = $entity->getProperties();
		foreach ($properties as $property) {
			$camelCase = $this->toCamelCase($property->getName());
			$studlyCase = $this->toStudlyCaps($property->getName());
			$typ = $this->tw->write($property->getType());

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
			$this->toStudlyCaps($entity->getName()),
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
			// TODO: exception
		}
	}

	// -- PSR-1 Case Adjustment --
	private function toCamelCase(string $s): string
	{
		switch (true) {
			case $this->isSnakeCase($s):
				return $this->snakeCaseToCamelCase($s);
			default:
				// TODO: exception
		}
	}

	private function toStudlyCaps(string $s): string
	{
		switch (true) {
			case $this->isSnakeCase($s):
				return $this->snakeCaseToStudlyCaps($s);
			default:
				// TODO: exception
		}
	}

	private function isSnakeCase(string $s): bool
	{
		return \preg_match('/^[a-zA-Z0-9]+(_[a-zA-Z0-9]+)*$/', $s) === 1;
	}

	private function snakeCaseToCamelCase(string $s): string
	{
		$s = \preg_replace('/[^a-zA-Z0-9]/', ' ', $s);
		$s = \preg_replace_callback('/\s([a-zA-Z])/', $this->toUpperFn(), $s);
		return \preg_replace('/[^a-zA-Z0-9]/', '', $s);
	}

	private function snakeCaseToStudlyCaps(string $s): string
	{
		$s = \preg_replace('/[^a-zA-Z0-9]/', ' ', $s);
		$s = \preg_replace_callback('/\s([a-zA-Z])/', $this->toUpperFn(), $s);
		return ucfirst(\preg_replace('/[^a-zA-Z0-9]/', '', $s));
	}

	private function toUpperFn(): callable
	{
		return function (array $matches): string {
			return strtoupper($matches[0]);
		};
	}
}
