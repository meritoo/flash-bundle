<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Meritoo\FlashBundle\DependencyInjection;

use Meritoo\Common\Utilities\Bundle;
use Meritoo\Common\Utilities\Reflection;
use Meritoo\FlashBundle\Exception\UnavailableFlashMessageTypeException;
use Meritoo\FlashBundle\MeritooFlashBundle;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration of this bundle
 *
 * @author    Meritoo <github@meritoo.pl>
 * @copyright Meritoo
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('meritoo_flash');

        $rootNode
            ->children()
                ->append($this->getTemplatesNode())
                ->append($this->getCssClassesNode())
                ->append($this->getFlashMessageTypesNode())
            ->end()
        ;

        return $treeBuilder;
    }

    /**
     * Returns node with configuration for templates
     *
     * @return NodeDefinition
     */
    private function getTemplatesNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('templates');
        $bundleName = Reflection::getClassName(MeritooFlashBundle::class, true);

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('many')
                    ->info('Path of template for many flash messages (with container)')
                    ->defaultValue(Bundle::getBundleViewPath('many', $bundleName))
                    /*
                     * Default value: "@MeritooFlash/many.html.twig"
                     */
                ->end()
                ->scalarNode('single')
                    ->info('Path of template for single/one flash message only')
                    ->defaultValue(Bundle::getBundleViewPath('single', $bundleName))
                    /*
                     * Default value: "@MeritooFlash/single.html.twig"
                     */
                ->end()
            ->end()
        ;

        return $rootNode;
    }

    /**
     * Returns node with configuration for CSS classes
     *
     * @return NodeDefinition
     */
    private function getCssClassesNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('css_classes');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('container')
                    ->info('CSS classes for the container for flash messages (with all flash messages)')
                    ->defaultValue('alerts')
                ->end()
                ->scalarNode('one_flash_message')
                    ->info('CSS classes, template for CSS classes actually, for one flash message. Placeholder is used to enter type of flash message.')
                    ->defaultValue('alert alert-%s')
                ->end()
            ->end()
        ;

        return $rootNode;
    }

    /**
     * Returns node with configuration for types of flash message
     *
     * @return NodeDefinition
     */
    private function getFlashMessageTypesNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('flash_message_types');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('available')
                    ->info('All available types of flash message')
                    ->useAttributeAsKey('name')
                    ->prototype('scalar')->end()
                    ->defaultValue([
                        'primary',
                        'secondary',
                        'success',
                        'info',
                        'warning',
                        'danger',
                        'light',
                        'dark',
                    ])
                ->end()
                ->scalarNode('positive')
                    ->info('Type of positive flash message')
                    ->defaultValue('success')
                ->end()
                ->scalarNode('negative')
                    ->info('Type of negative flash message')
                    ->defaultValue('danger')
                ->end()
                ->scalarNode('neutral')
                    ->info('Type of neutral flash message')
                    ->defaultValue('info')
                ->end()
            ->end()
            ->validate()
                ->ifTrue(function ($values) {
                    $availableTypes = $values['available'];

                    $verifyNodes = [
                        'positive',
                        'negative',
                        'neutral',
                    ];

                    foreach ($verifyNodes as $nodeName) {
                        $type = $values[$nodeName];

                        /*
                         * Oops, unavailable type of flash message
                         */
                        if (false === \in_array($type, $availableTypes, true)) {
                            return true;
                        }
                    }

                    return false;
                })
                ->then(function ($values) {
                    $type = sprintf('%s OR %s OR %s', $values['positive'], $values['negative'], $values['neutral']);
                    throw UnavailableFlashMessageTypeException::create($type, $values['available']);
                })
            ->end()
        ;

        return $rootNode;
    }
}
