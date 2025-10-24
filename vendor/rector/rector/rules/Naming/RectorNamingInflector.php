<?php

declare (strict_types=1);
namespace Rector\Naming;

use RectorPrefix202510\Doctrine\Inflector\Inflector;
use RectorPrefix202510\Nette\Utils\Strings;
final class RectorNamingInflector
{
    /**
     * @readonly
     */
    private Inflector $inflector;
    /**
     * @var string
     * @see https://regex101.com/r/VqVvke/3
     */
    private const DATA_INFO_SUFFIX_REGEX = '#^(?<prefix>.+)(?<suffix>Data|Info)$#';
    public function __construct(Inflector $inflector)
    {
        $this->inflector = $inflector;
    }
    public function singularize(string $name): string
    {
        $matches = Strings::match($name, self::DATA_INFO_SUFFIX_REGEX);
        if ($matches === null) {
            return $this->inflector->singularize($name);
        }
        $singularized = $this->inflector->singularize($matches['prefix']);
        $uninflectable = $matches['suffix'];
        return $singularized . $uninflectable;
    }
}
