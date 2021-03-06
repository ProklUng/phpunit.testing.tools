<?php

namespace Prokl\TestingTools\Tools;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use JsonException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Router;

/**
 * Class PHPUnitAPI
 * Хэлперы для тестирования API.
 * @package Prokl\TestingTools\Tools
 *
 * @since 24.09.2020 Актуализация.
 * @since 29.09.2020 Актуализация.
 * @since 28.05.2021 Рефакторинг.
 */
class PHPUnitAPI
{
    /**
     * @var string $host Базовый хост.
     */
    public static $host = '';

    /**
     * @var Router|null $router
     */
    public static $router;

    /**
     * Вызов API.
     *
     * @param string $url      Точка входа.
     * @param array  $arParams Параметры.
     *
     * @return mixed
     * @throws GuzzleException|JsonException
     */
    public static function apiCall(string $url, array $arParams)
    {
        $client = new Client(['base_uri' => static::$host]);

        try {
            $response = $client->request('POST', $url, $arParams);
        } catch (RequestException  $e) {
            if ($e->hasResponse()) {
                return json_decode($e->getResponse()->getBody(), true, 512, JSON_THROW_ON_ERROR);
            }

            return [];
        }

        return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Вызов API. Расчет на сырой HTML.
     *
     * @param string $url      Точка входа.
     * @param array  $arParams Параметры.
     *
     * @return mixed
     * @throws GuzzleException
     */
    public static function apiCallHtml(string $url, array $arParams = [])
    {
        $client = new Client(['base_uri' => static::$host]);

        try {
            $response = $client->request('GET', $url, $arParams);
        } catch (RequestException  $e) {
            return $e->getResponse()->getBody()->getContents();
        }

        return $response->getBody()->getContents();
    }

    /**
     * Путь к роуту.
     *
     * @param string $routeName Название роута.
     * @param array  $arParams  Параметры.
     *
     * @return string
     * @throws Exception
     */
    public static function getURLRouting(string $routeName, array $arParams = []) : string
    {
        if (static::$router === null) {
            throw new \Exception('Router not initialized.');
        }

        if (!$routeName) {
            return '';
        }

        $context = new RequestContext();
        $generator = new UrlGenerator(static::$router->getRouteCollection(), $context);

        return $generator->generate($routeName, $arParams);
    }
}
