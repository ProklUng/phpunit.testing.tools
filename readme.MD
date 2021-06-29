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
            '/Resources/config',
            [new SampleCompilerPass()] // Опциональный параметр - кастомные compiler passes 
        );

        parent::setUp();
    }
}
```

Отнаследовать от него тест.

Подгрузятся конфиги сервисов из указанных файлов по указанному пути (относительно DOCUMENT_ROOT тестов).

## CommandTestCase

Базовый класс для тестирования консольных команд.

Методы:

- `executeCommand(Command $commandInstance, string $commandName, array $params = [])` - вернет то, что команда
вывела на экран.
- `runCommand(Command $command, $input = [])` - вернет результат выполнения метода `execute` команды.

## Трэйт DefaultDataProviders

Несколько общих дата-провайдеров

- `provideEmptyValue` - пустые значения.
- `provideEmptyScalarValue` - пустые скалярные значения
- `provideBooleanValue` - булевы значения
- `provideDateTimeInstance` - инстанц DateTime
- `provideNotExistingFilePath` - путь к несуществующему файлу

## Мокер функций

Обертка [над](https://github.com/php-mock/php-mock-mockery).

Пример (в тесте, унаследованном от `BaseTestCase`):

```php
        // Замокается полностью (т.е. не важно с какими параметрами пройдет вызов) функция in_the_loop 
        $this->mockerFunctions->setNamespace('\Tests\API')
            ->full('in_the_loop', true)
            ->mock();
```

`Namespace` - пространство имен, в котором мокается функция. 

Или частичное моканье (в зависимости от аргументов):

```php
       // При вызове  get_cat_name с аргументом $this->idCategory вернет Mocked category
       $this->mockerFunctions->setNamespace('Test\API\Entity')
            ->partial('get_cat_name', 'Mocked category', $this->idCategory)
            ->partial('category_description', 'Mocked category description', $this->idCategory)
            ->mock();
```

При использовании этой фичи рекомендуется (во избежании проблем) на тест ставить аннотации:

```php
    /**
     * data()
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
```

## Трэйт ServiceLocatorConstructorTrait

Конструктор сервис-локаторов Symfony для тестов.

Метод:

- `constructServiceLocator(array $config)` - где `$config` массив вида:

```php
$object = new ClassName();

$config = [
 'service_key' => ClassName::class,
 'service_key2' => $object,

];
```

Если передать название класса в конфиге, то внутри метода класс будет инстанцирован.