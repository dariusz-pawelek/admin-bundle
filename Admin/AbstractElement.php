<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\AdminBundle\Admin;

use FSi\Bundle\AdminBundle\Exception\MissingOptionException;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractElement implements Element
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var array
     */
    private $unresolvedOptions;

    public function __construct(array $options = [])
    {
        $this->unresolvedOptions = $options;
    }

    public function getRouteParameters(): array
    {
        return [
            'element' => $this->getId(),
        ];
    }

    public function getOption(string $name)
    {
        $this->resolveOptions();

        if (!$this->hasOption($name)) {
            throw new MissingOptionException(sprintf(
                'Option with name "%s" does not exist in element "%s"',
                $name,
                get_class($this)
            ));
        }

        return $this->options[$name];
    }

    public function getOptions(): array
    {
        $this->resolveOptions();

        return $this->options;
    }

    public function hasOption($name): bool
    {
        $this->resolveOptions();
        return isset($this->options[$name]);
    }

    private function resolveOptions(): void
    {
        if (!is_array($this->options)) {
            $optionsResolver = new OptionsResolver();
            $this->configureOptions($optionsResolver);
            $this->options = $optionsResolver->resolve($this->unresolvedOptions);
            unset($this->unresolvedOptions);
        }
    }
}
