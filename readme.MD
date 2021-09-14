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
            [new SampleCompilerPass()], // Опциональный параметр - кастомные compiler passes,
            'dev', // Окружение. По умолчанию - dev
            true   // Debug. По умолчанию - true,
            ['service_to_mock'] // Сервисы, подлежащие мокингу (см. подраздел Моки сервисов)
        );

        parent::setUp();
    }
}
```

Отнаследовать от него тест.

Подгрузятся конфиги сервисов из указанных файлов по указанному пути (относительно DOCUMENT_ROOT тестов).

### Мокинг сервисов для функциональных тестов

Механизм (на базе создания прокси-сервисов по заданному списку) взят из [бандла](https://github.com/Happyr/service-mocking) 
и адаптирован под локальные нужды.

```php
use Prokl\TestingTools\Tools\ServiceMocker;
use Prokl\TestingTools\Base\BaseTestCase;

class MyTest extends BaseTestCase
{
    use RestoreServiceContainer;

        protected function setUp(): void
        {
            parent::setUp();
    
            $this->container = BuildContainer::getTestContainer(
                [
                    'test_container.yaml'
                ],
                '/../../../../tests/Standalone/Resource/config',
                [],
                'dev',
                true,
                ['filesystem.local.adapter'] // Сервис, который будет заменен моком.
            );
    }

    public function testFoo()
    {
        // For all calls
         ServiceMock::all($this->container->get('filesystem.local.adapter'), 'getAdapter', function () {
             return new Local(
                 $_SERVER['DOCUMENT_ROOT'] .  '/test/');
         });
 
         $result = $this->container->get('filesystem.local.adapter');

        // For only the next call
         ServiceMock::next($this->container->get('filesystem.local.adapter'), 'getAdapter', function () {
             return new Local(
                 $_SERVER['DOCUMENT_ROOT'] .  '/test/');
         });
 
        // This will queue a new callable
         ServiceMock::next($this->container->get('filesystem.local.adapter'), 'getAdapter', function () {
             throw new \InvalidArgument('getAdapter can call once time!');
         });

        $mock = // create a PHPUnit mock or any other mock you want.
        ServiceMocker::swap($this->container->get('filesystem.local.adapter'), $mock);

        // ...
        
         $service = $this->container->get('filesystem.local.adapter');
         $result = $service->getAdapter(); // Метод сервиса (или сервис целиком) подменен.   
    }

    protected function tearDown(): void
    {
        // To make sure we don't affect other tests
        ServiceMock::resetAll();
        // You can include the RestoreServiceContainer trait to automatically reset services
    }
}
```

## BootTestKernelTrait

```php

use Prokl\TestingTools\Traits\BootTestKernelTrait;

class ExampleTest extends \Prokl\TestingTools\Base\BaseTestCase
{
    use BootTestKernelTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $container = new ContainerBuilder();
        
        // ... Наполнение тестового контейнера.
        
        self::$kernel = $this->bootTestKernel($container);
    }
}
```

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

## Console test

Форк [пакета](https://github.com/kbond/console-test)

```php
use App\Command\CreateUserCommand;
use Prokl\TestingTools\Base\BaseTestCase;
use Prokl\TestingTools\Tools\Console\InteractsWithConsole;
use Prokl\TestingTools\Traits\BootTestKernelTrait;

class CreateUserCommandTest extends BaseTestCase
{
    use InteractsWithConsole;
    use BootTestKernelTrait;
        
    protected function setUp(): void
    {
        parent::setUp();

        $container = new ContainerBuilder();

        $container->setDefinition(
            IntegrityCheck::class,
            new Definition(IntegrityCheck::class, [])
        )->setTags(['console.command' => ['command' => 'module:еуые']])->setPublic(true);


        self::$kernel = $this->bootTestKernel($container);

        $this->cliApplication = new \Symfony\Bundle\FrameworkBundle\Console\Application(self::$kernel);
        $this->cliApplication->add($container->get(IntegrityCheck::class));
    }
    
    public function test_can_create_user(): void
    {
        $this->executeConsoleCommand('create:user kbond --admin --role=ROLE_EMPLOYEE --role=ROLE_MANAGER')
            ->assertSuccessful() // command exit code is 0
            ->assertOutputContains('Creating admin user "kbond"')
            ->assertOutputContains('with roles: ROLE_EMPLOYEE, ROLE_MANAGER')
            ->assertOutputNotContains('regular user')
        ;

        // advanced usage
        $this->consoleCommand(CreateUserCommand::class) // can use the command class or "name"
            ->splitOutputStreams() // by default stdout/stderr are combined, this options splits them
            ->addArgument('kbond')
            ->addOption('--admin') // with or without "--" prefix
            ->addOption('role', ['ROLE_EMPLOYEE', 'ROLE_MANAGER'])
            ->addOption('-R') // shortcut options require the "-" prefix
            ->addOption('-vv') // by default, output has normal verbosity, use the standard options to change (-q, -v, -vv, -vvv)
            ->addOption('--ansi') // by default, output is undecorated, use this option to decorate
            ->execute() // run the command
            ->assertSuccessful()
            ->assertStatusCode(0) // equivalent to ->assertSuccessful()
            ->assertOutputContains('Creating admin user "kbond"')
            ->assertErrorOutputContains('this is in stderr') // used in conjunction with ->splitOutputStreams()
            ->assertErrorOutputNotContains('admin user') // used in conjunction with ->splitOutputStreams()
            ->dump() // dump() the status code/outputs and continue
            ->dd() // dd() the status code/outputs
        ;

        // testing interactive commands
        $this->executeConsoleCommand('create:user', ['kbond'])
            ->assertSuccessful()
            ->assertOutputContains('Creating regular user "kbond"')
        ;
        
        // advanced testing interactive commands
        $this->consoleCommand(CreateUserCommand::class)
            ->addInput('kbond')
            ->addOption('--no-interaction') // commands are run interactively if input is provided, use this option to disable
            ->execute()
            ->assertSuccessful()
            ->assertOutputContains('Creating regular user "kbond"')
        ;
        
        // access result
        $result = $this->executeConsoleCommand('create:user');

        $result->statusCode();
        $result->output(); 
        $result->errorOutput(); 
    }
}
```

Или:

```php
use App\Command\CreateUserCommand;
use PHPUnit\Framework\TestCase;
use Prokl\TestingTools\Tools\Console\TestCommand;

class CreateUserCommandTest extends TestCase
{
    public function test_can_create_user(): void
    {
        TestCommand::for(new CreateUserCommand(/** args... */))
            ->splitOutputStreams() // by default stdout/stderr are combined, this options splits them
            ->addArgument('kbond')
            ->addOption('--admin') // with or without "--" prefix
            ->addOption('role', ['ROLE_EMPLOYEE', 'ROLE_MANAGER'])
            ->addOption('-R') // shortcut options require the "-" prefix
            ->addOption('-vv') // by default, output has normal verbosity, use the standard options to change (-q, -v, -vv, -vvv)
            ->addOption('--ansi') // by default, output is undecorated, use this option to decorate
            ->execute()
            ->assertSuccessful()
            ->assertStatusCode(0) // equivalent to ->assertSuccessful()
            ->assertOutputContains('Creating admin user "kbond"')
            ->assertErrorOutputContains('this is in stderr') // used in conjunction with ->splitOutputStreams()
            ->assertErrorOutputNotContains('admin user') // used in conjunction with ->splitOutputStreams()
            ->dump() // dump() the status code/outputs and continue
            ->dd() // dd() the status code/outputs
        ;
        
        // testing interactive commands
        TestCommand::for(new CreateUserCommand(/** args... */))
            ->addInput('kbond')
            ->addOption('--no-interaction') // commands are run interactively if input is provided, use this option to disable
            ->execute()
            ->assertSuccessful()
            ->assertOutputContains('Creating regular user "kbond"')
        ;

        // access result
        $result = TestCommand::for(new CreateUserCommand(/** args... */))->execute();

        $result->statusCode();
        $result->output(); 
        $result->errorOutput(); 
    }
}
```