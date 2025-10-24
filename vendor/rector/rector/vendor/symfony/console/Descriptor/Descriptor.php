<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RectorPrefix202510\Symfony\Component\Console\Descriptor;

use RectorPrefix202510\Symfony\Component\Console\Application;
use RectorPrefix202510\Symfony\Component\Console\Command\Command;
use RectorPrefix202510\Symfony\Component\Console\Exception\InvalidArgumentException;
use RectorPrefix202510\Symfony\Component\Console\Input\InputArgument;
use RectorPrefix202510\Symfony\Component\Console\Input\InputDefinition;
use RectorPrefix202510\Symfony\Component\Console\Input\InputOption;
use RectorPrefix202510\Symfony\Component\Console\Output\OutputInterface;
/**
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @internal
 */
abstract class Descriptor implements DescriptorInterface
{
    protected OutputInterface $output;
    public function describe(OutputInterface $output, object $object, array $options = []): void
    {
        $this->output = $output;
        switch (\true) {
            case $object instanceof InputArgument:
                $this->describeInputArgument($object, $options);
                break;
            case $object instanceof InputOption:
                $this->describeInputOption($object, $options);
                break;
            case $object instanceof InputDefinition:
                $this->describeInputDefinition($object, $options);
                break;
            case $object instanceof Command:
                $this->describeCommand($object, $options);
                break;
            case $object instanceof Application:
                $this->describeApplication($object, $options);
                break;
            default:
                throw new InvalidArgumentException(\sprintf('Object of type "%s" is not describable.', get_debug_type($object)));
        }
    }
    protected function write(string $content, bool $decorated = \false): void
    {
        $this->output->write($content, \false, $decorated ? OutputInterface::OUTPUT_NORMAL : OutputInterface::OUTPUT_RAW);
    }
    /**
     * Describes an InputArgument instance.
     */
    abstract protected function describeInputArgument(InputArgument $argument, array $options = []): void;
    /**
     * Describes an InputOption instance.
     */
    abstract protected function describeInputOption(InputOption $option, array $options = []): void;
    /**
     * Describes an InputDefinition instance.
     */
    abstract protected function describeInputDefinition(InputDefinition $definition, array $options = []): void;
    /**
     * Describes a Command instance.
     */
    abstract protected function describeCommand(Command $command, array $options = []): void;
    /**
     * Describes an Application instance.
     */
    abstract protected function describeApplication(Application $application, array $options = []): void;
}
