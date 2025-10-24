<?php

declare (strict_types=1);
namespace Rector\PHPUnit\CodeQuality\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use Rector\PHPUnit\NodeAnalyzer\TestsNodeAnalyzer;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Rector\PHPUnit\Tests\CodeQuality\Rector\MethodCall\AssertCompareOnCountableWithMethodToAssertCountRector\AssertCompareOnCountableWithMethodToAssertCountRectorTest
 */
final class AssertCompareOnCountableWithMethodToAssertCountRector extends AbstractRector
{
    /**
     * @readonly
     */
    private TestsNodeAnalyzer $testsNodeAnalyzer;
    public function __construct(TestsNodeAnalyzer $testsNodeAnalyzer)
    {
        $this->testsNodeAnalyzer = $testsNodeAnalyzer;
    }
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replaces use of assertSame and assertEquals on Countable objects with count method', [new CodeSample(<<<'CODE_SAMPLE'
$this->assertSame(1, $countable->count());
CODE_SAMPLE
, <<<'CODE_SAMPLE'
$this->assertCount(1, $countable);
CODE_SAMPLE
), new CodeSample('$this->assertSame(10, count($anything), "message");', '$this->assertCount(10, $anything, "message");')]);
    }
    /**
     * @return array<class-string<MethodCall|StaticCall>>
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class, StaticCall::class];
    }
    /**
     * @param MethodCall|StaticCall $node
     * @return \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall|null
     */
    public function refactor(Node $node)
    {
        if (!$this->testsNodeAnalyzer->isPHPUnitMethodCallNames($node, ['assertSame', 'assertNotSame', 'assertEquals', 'assertNotEquals'])) {
            return null;
        }
        if ($node->isFirstClassCallable()) {
            return null;
        }
        $assertArgs = $node->getArgs();
        if (count($assertArgs) < 2) {
            return null;
        }
        $comparedExpr = $assertArgs[1]->value;
        if ($comparedExpr instanceof FuncCall && $this->isNames($comparedExpr->name, ['count', 'sizeof', 'iterator_count'])) {
            $countArg = $comparedExpr->getArgs()[0];
            $assertArgs[1] = new Arg($countArg->value);
            $node->args = $assertArgs;
            $this->renameMethod($node);
            return $node;
        }
        if ($comparedExpr instanceof MethodCall && $this->isName($comparedExpr->name, 'count') && $comparedExpr->getArgs() === []) {
            $type = $this->getType($comparedExpr->var);
            if ((new ObjectType('Countable'))->isSuperTypeOf($type)->yes()) {
                $args = $assertArgs;
                $args[1] = new Arg($comparedExpr->var);
                $node->args = $args;
                $this->renameMethod($node);
            }
        }
        return null;
    }
    /**
     * @param \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall $node
     */
    private function renameMethod($node): void
    {
        if ($this->isNames($node->name, ['assertSame', 'assertEquals'])) {
            $node->name = new Identifier('assertCount');
        } elseif ($this->isNames($node->name, ['assertNotSame', 'assertNotEquals'])) {
            $node->name = new Identifier('assertNotCount');
        }
    }
}
