# Кастомные инструменты для PHPUnit тестов

**INTERNAL**

## Установка

`composer require --dev proklung/phpunit-testing-tools`

## Всякое

### Как построить тестовый контейнер

Создать класс:

```php

use Prokl\TestingTools\Base\BaseTestCase;
use Prokl\TestingTools\Tools\Container\BuildContainer;

class ContainerAwareBaseTestCase extends BaseTestCase
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->container = static::$testContainer = BuildContainer::getTestContainer(
            [
                'dev/test_container.yaml',
                'dev/local.yaml'
            ],
            '/Resources/config'
        );

        parent::setUp();
    }
}
```

Отнаследовать от него тест.

Подгрузятся конфиги сервисов из указанных файлов.

