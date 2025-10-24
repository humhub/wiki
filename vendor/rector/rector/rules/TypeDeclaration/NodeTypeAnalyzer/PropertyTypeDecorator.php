<?php

declare (strict_types=1);
namespace Rector\TypeDeclaration\NodeTypeAnalyzer;

use PhpParser\Node\ComplexType;
use PhpParser\Node\Expr;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Property;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\UnionType;
use PHPStan\Type\VerbosityLevel;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTypeChanger;
use Rector\Php\PhpVersionProvider;
use Rector\PhpParser\Node\NodeFactory;
use Rector\ValueObject\PhpVersionFeature;
final class PropertyTypeDecorator
{
    /**
     * @readonly
     */
    private PhpDocTypeChanger $phpDocTypeChanger;
    /**
     * @readonly
     */
    private PhpVersionProvider $phpVersionProvider;
    /**
     * @readonly
     */
    private NodeFactory $nodeFactory;
    public function __construct(PhpDocTypeChanger $phpDocTypeChanger, PhpVersionProvider $phpVersionProvider, NodeFactory $nodeFactory)
    {
        $this->phpDocTypeChanger = $phpDocTypeChanger;
        $this->phpVersionProvider = $phpVersionProvider;
        $this->nodeFactory = $nodeFactory;
    }
    /**
     * @param \PhpParser\Node\Name|\PhpParser\Node\ComplexType|\PhpParser\Node\Identifier $typeNode
     */
    public function decoratePropertyUnionType(UnionType $unionType, $typeNode, Property $property, PhpDocInfo $phpDocInfo, bool $changeVarTypeFallback = \true): void
    {
        if (!TypeCombinator::containsNull($unionType)) {
            if ($this->phpVersionProvider->isAtLeastPhpVersion(PhpVersionFeature::UNION_TYPES)) {
                $property->type = $typeNode;
                return;
            }
            if ($changeVarTypeFallback) {
                $this->phpDocTypeChanger->changeVarType($property, $phpDocInfo, $unionType);
            }
            return;
        }
        $property->type = $typeNode;
        $propertyProperty = $property->props[0];
        // add null default
        if (!$propertyProperty->default instanceof Expr) {
            $propertyProperty->default = $this->nodeFactory->createNull();
        }
        // has array with defined type? add docs
        if (!$this->isDocBlockRequired($unionType)) {
            return;
        }
        if (!$changeVarTypeFallback) {
            return;
        }
        $this->phpDocTypeChanger->changeVarType($property, $phpDocInfo, $unionType);
    }
    private function isDocBlockRequired(UnionType $unionType): bool
    {
        foreach ($unionType->getTypes() as $unionedType) {
            if ($unionedType->isArray()->yes()) {
                $describedArray = $unionedType->describe(VerbosityLevel::value());
                if ($describedArray !== 'array') {
                    return \true;
                }
            }
        }
        return \false;
    }
}
