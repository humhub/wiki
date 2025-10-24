<?php

declare (strict_types=1);
namespace Rector\Naming\ValueObjectFactory;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;
use Rector\Naming\ValueObject\PropertyRename;
use Rector\NodeNameResolver\NodeNameResolver;
use RectorPrefix202510\Webmozart\Assert\InvalidArgumentException;
final class PropertyRenameFactory
{
    /**
     * @readonly
     */
    private NodeNameResolver $nodeNameResolver;
    public function __construct(NodeNameResolver $nodeNameResolver)
    {
        $this->nodeNameResolver = $nodeNameResolver;
    }
    public function createFromExpectedName(Class_ $class, Property $property, string $expectedName): ?PropertyRename
    {
        $currentName = $this->nodeNameResolver->getName($property);
        $className = (string) $this->nodeNameResolver->getName($class);
        try {
            return new PropertyRename($property, $expectedName, $currentName, $class, $className, $property->props[0]);
        } catch (InvalidArgumentException $exception) {
        }
        return null;
    }
}
