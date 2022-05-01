<?php

namespace PhpHephaestus\IO\Entity\Writer\PHP;

use PhpHephaestus\IntermediateRepresentation\Entity;
use PhpHephaestus\IntermediateRepresentation\Type\Scalar\Binary;
use PhpHephaestus\IntermediateRepresentation\Type\Scalar\Currency;
use PhpHephaestus\IntermediateRepresentation\Type\Scalar\Date;
use PhpHephaestus\IntermediateRepresentation\Type\Scalar\DateTime;
use PhpHephaestus\IntermediateRepresentation\Type\Scalar\Enumeration;
use PhpHephaestus\IntermediateRepresentation\Type\Scalar\Float_;
use PhpHephaestus\IntermediateRepresentation\Type\Scalar\Integer;
use PhpHephaestus\IntermediateRepresentation\Type\Scalar\Time;
use PhpHephaestus\IO\Entity\Writer;
use PhpHephaestus\PSR\PSR1;
use PhpParser\Comment\Doc;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\PrettyPrinter\Standard;
use RuntimeException;

final class SimplePHPUnit implements Writer
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
		$psr1 = new PSR1();
		$providerFnName = 'accessorsAndMutatorsProvider';
		$namespace = new Namespace_(
			new Name($this->namespace),
			[
				new Use_(
					[
						new UseUse(new Name('PHPUnit\\Framework\\TestCase')),
					]
				),
				new Class_(
					$psr1->toStudlyCaps($entity->getName()) . 'Test',
					[
						'flags' => Class_::MODIFIER_FINAL,
						'extends' => new Name('TestCase'),
						'stmts' => [
							$this->testAccessorsAndMutatorsClassMethodStmt(
								$psr1,
								$entity,
								$providerFnName
							),
							$this->accessorsAndMutatorsProviderClassMethodStmt(
								$psr1,
								$entity,
								$providerFnName
							),
						],
					]
				),
			]
		);

		$prettyPrinter = new Standard();
		$code = $prettyPrinter->prettyPrintFile([$namespace]);
		$n = $this->w->write($code);
		if ($n !== strlen($code)) {
			throw new RuntimeException('Write failed: incomplete write');
		}
	}

	private function testAccessorsAndMutatorsClassMethodStmt(
		PSR1 $psr1,
		Entity $entity,
		string $providerFnName
	): ClassMethod {
		return new ClassMethod(
			'testAccessorsAndMutators',
			[
				'flags' => Class_::MODIFIER_PUBLIC,
				'params' => [
					new Param(
						new Variable('fnSuffix'),
						null,
						new Identifier('string')
					),
					new Param(
						new Variable('compareFn'),
						null,
						new Identifier('callable')
					),
					new Param(
						new Variable('expected'),
					),
				],
				'returnType' => 'void',
				'stmts' => [
					new Expression(
						new Assign(
							new Variable('obj'),
							new New_(
								new Name($psr1->toStudlyCaps($entity->getName()))
							)
						)
					),
					new Expression(
						new FuncCall(
							new Name\FullyQualified('call_user_func'),
							[
								new Arg(
									new Array_(
										[
											new ArrayItem(
												new Variable('obj')
											),
											new ArrayItem(
												new Concat(
													new String_('set'),
													new Variable('fnSuffix')
												)
											)
										]
									)
								),
								new Arg(new Variable('expected'))
							]
						)
					),
					new Expression(
						new Assign(
							new Variable('result'),
							new FuncCall(
								new Name\FullyQualified('call_user_func'),
								[
									new Arg(
										new Array_(
											[
												new ArrayItem(
													new Variable('obj')
												),
												new ArrayItem(
													new Concat(
														new String_('get'),
														new Variable('fnSuffix')
													)
												)
											]
										)
									),
								]
							)
						)
					),
					new Expression(
						new MethodCall(
							new Variable('this'),
							new Identifier('assertTrue'),
							[
								new Arg(
									new FuncCall(
										new Variable('compareFn'),
										[
											new Arg(new Variable('expected')),
											new Arg(new Variable('result')),
										]
									)
								)
							]
						)
					)
				],
			],
			[
				'comments' => [
					new Doc("/**\n * @dataProvider {$providerFnName}\n */")
				]
			]
		);
	}

	private function accessorsAndMutatorsProviderClassMethodStmt(
		PSR1 $psr1,
		Entity $entity,
		string $providerFnName
	): ClassMethod {
		$testCaseStmts = [];
		$properties = $entity->getProperties();
		foreach ($properties as $property) {
			$t = $property->getType();
			$compareFnStmt = null;
			$expectedStmt = null;
			switch (true) {
				case $t instanceof Binary:
				case $t instanceof Enumeration;
				case $t instanceof \PhpHephaestus\IntermediateRepresentation\Type\Scalar\String_;
					$compareFnStmt = new Closure(
						[
							'params' => [
								new Param(
									new Variable('a'),
									null,
									new Identifier('string')
								),
								new Param(
									new Variable('b'),
									null,
									new Identifier('string')
								),
							],
							'returnType' => new Identifier('bool'),
							'stmts' => [
								new Return_(
									new Identical(
										new FuncCall(
											new Name\FullyQualified('strcmp'),
											[
												new Arg(new Variable('a')),
												new Arg(new Variable('b')),
											]
										),
										new LNumber(0)
									)
								)
							],
						]
					);
					$expectedStmt = new FuncCall(new Name\FullyQualified('uniqid'));
					break;
				case $t instanceof Currency:
				case $t instanceof Integer;
					$compareFnStmt = new Closure(
						[
							'params' => [
								new Param(
									new Variable('a'),
									null,
									new Identifier('int')
								),
								new Param(
									new Variable('b'),
									null,
									new Identifier('int')
								),
							],
							'returnType' => new Identifier('bool'),
							'stmts' => [
								new Return_(
									new Identical(
										new Variable('a'),
										new Variable('b')
									)
								)
							],
						]
					);
					$expectedStmt = new FuncCall(
						new Name\FullyQualified('random_int'),
						[
							new Arg(new LNumber(-1000)),
							new Arg(new LNumber(1000)),
						]
					);
					break;
				case $t instanceof Date;
				case $t instanceof DateTime;
				case $t instanceof Time;
					$compareFnStmt = new Closure(
						[
							'params' => [
								new Param(
									new Variable('a'),
									null,
									new Name\FullyQualified('DateTime')
								),
								new Param(
									new Variable('b'),
									null,
									new Name\FullyQualified('DateTime')
								),
							],
							'returnType' => new Identifier('bool'),
							'stmts' => [
								new Return_(
									new Identical(
										new MethodCall(
											new Variable('a'),
											new Name('getTimestamp')
										),
										new MethodCall(
											new Variable('b'),
											new Name('getTimestamp')
										),
									)
								)
							],
						]
					);
					$expectedStmt =
						new MethodCall(
							new New_(new Name\FullyQualified('DateTime')),
							new Identifier('add'),
							[
								new Arg(
									new New_(
										new Name\FullyQualified('DateInterval'),
										[
											new Arg(
												new FuncCall(
													new Name\FullyQualified('sprintf'),
													[
														new Arg(new String_('P%dD')),
														new Arg(
															new FuncCall(
																new Name\FullyQualified('random_int'),
																[
																	new Arg(new LNumber(0)),
																	new Arg(new LNumber(9)),
																]
															)
														),
													]
												)
											),
										]
									)
								)
							]
						);
					break;
				case $t instanceof Float_;
					$compareFnStmt = new Closure(
						[
							'params' => [
								new Param(
									new Variable('a'),
									null,
									new Identifier('float')
								),
								new Param(
									new Variable('b'),
									null,
									new Identifier('float')
								),
							],
							'returnType' => new Identifier('bool'),
							'stmts' => [
								new Return_(
									new Identical(
										new Variable('a'),
										new Variable('b')
									)
								)
							],
						]
					);
					$expectedStmt = new FuncCall(
						new Name\FullyQualified('rand'),
						[
							new Arg(new DNumber(-10)),
							new Arg(new DNumber(10)),
						]
					);
					break;
				default:
					continue 2;
			}

			$testCaseStmts[] = new ArrayItem(
				new Array_(
					[
						// $fnSuffix
						new ArrayItem(new String_($psr1->snakeCaseToStudlyCaps($property->getName()))),
						// $compareFn
						new ArrayItem($compareFnStmt),
						// $expected
						new ArrayItem($expectedStmt),
					]
				),
				new String_($property->getName())
			);
		}

		return new ClassMethod(
			$providerFnName,
			[
				'flags' => Class_::MODIFIER_PUBLIC,
				'returnType' => 'array',
				'stmts' => [
					new Return_(
						new Array_($testCaseStmts)
					)
				],
			]
		);
	}
}
