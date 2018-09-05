# Meritoo Flash Bundle

Mechanisms, extensions and resources related to Symfony Flash Messages (https://symfony.com/doc/current/controller.html#flash-messages)

# Configuration

### Introduction

Configuration parameters are loaded by `Meritoo\CommonBundle\DependencyInjection\Configuration` class that implements `Symfony\Component\Config\Definition\ConfigurationInterface` and uses `Symfony\Component\Config\Definition\Builder\TreeBuilder` to build structure of configuration parameters.

Name of root node: `meritoo_flash`.

### All parameters of this bundle with default values

```yaml
meritoo_flash:
    templates:
        many: '@MeritooFlash/many.html.twig'
        single: '@MeritooFlash/single.html.twig'
    css_classes:
        container: alerts
        one_flash_message: alert alert-%s
    flash_message_types:
    	available:
            - primary
            - secondary
            - success
            - info
            - warning
            - danger
            - light
            - dark
        positive: success
        negative: danger
        neutral: info
```

### Available parameters

* meritoo_flash.templates.many

	> Path of template for many flash messages (with container)

    Default value: `@MeritooFlash/many.html.twig`

* meritoo_flash.templates.single

	> Path of template for single/one flash message only

    Default value: `@MeritooFlash/single.html.twig`

* meritoo_flash.css_classes.container

    > CSS classes for the container for flash messages (with all flash messages)

    Default value: `alerts`

* meritoo_flash.css_classes.one_flash_message

    > CSS classes, template for CSS classes actually, for one flash message. Placeholder is used to enter type of flash message.

    Default value: `alert alert-%s`

* meritoo_flash.flash_message_types

    > All available types of flash message

    Default value:
    ```
    - primary
    - secondary
    - success
    - info
    - warning
    - danger
    - light
    - dark
    ```

* meritoo_flash.flash_message_types.positive

	> Type of positive flash message

    Default value: `success`

* meritoo_flash.flash_message_types.negative

	> Type of negative flash message

    Default value: `danger`

* meritoo_flash.flash_message_types.neutral

	> Type of neutral flash message

    Default value: `info`

# More

1. [**Configuration**](Configuration.md)

[&lsaquo; Back to `Readme`](../README.md)
