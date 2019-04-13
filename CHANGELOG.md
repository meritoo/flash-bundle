# Meritoo Flash Bundle

Mechanisms, extensions and resources related to Symfony Flash Messages
(https://symfony.com/doc/current/controller.html#flash-messages)

# 0.1.3

1. Phing > update configuration
2. Docker > docker-compose.yml > add "phpunit" service > used to run PHPUnit's tests
3. Travis CI > run many tasks using Phing (instead of PHPUnit only)
4. Fix integration with [Coveralls](https://www.coveralls.io) (available as the badge in [README.md](README.md))
5. Implement [PHPStan](https://github.com/phpstan/phpstan)
6. Implement [Psalm](https://github.com/vimeo/psalm)
7. Fix "The spaceless tag is deprecated since Twig 2.7, use the spaceless filter instead" deprecation notice
8. PHPUnit > increase code coverage
9. PHP Coding Standards Fixer > update configuration

# 0.1.2

1. Phing > tests > missing path of directory with code coverage report
2. Tests > add missing tests
3. Docker > Dockerfile > remove not working the Handy Apt Terminal Progress Bar
4. Docker > Dockerfile > fix installation of Composer
5. Phing > update configuration files
6. Implement Mutation Testing Framework (infection/infection package)
7. Twig extension > render many flash messages > use path of template for single/one flash message defined in 
configuration

# 0.1.1

1. Travis CI > update configuration (You are using the deprecated option "dev". Dev packages are installed by default
now.)
2. Twig function meritoo_flash_message_has_messages() > returns information if there are any flash messages to 
display (in bag/container stored in session)

# 0.1.0

1. Implement Docker
2. Update Readme
3. Add documentation
4. Integrate with Travis CI
5. Add composer.json
6. Add .gitignore
7. Add configuration of PHP Coding Standards Fixer
8. Implement Phing
9. Add PHPUnit configuration
10. Add main class of bundle
11. Add configuration of bundle
12. Tests > packages, configuration & kernel
13. Templates & Twg extensions
14. FlashMessageService. Service related to flash messages.
