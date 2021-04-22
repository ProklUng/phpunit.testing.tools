<?php

namespace Prokl\TestingTools\Traits;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use JsonException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class PHPUnitAPI
 * Хэлперы для тестирования API.
 * @package Tests
 *
 * @since 24.09.2020 Актуализация.
 * @since 29.09.2020 Актуализация.
 */
class PHPUnitAPI
{
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
        $client = new Client(['base_uri' => container()->getParameter('test.host.url')]);

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
     * @param string $url     Точка входа.
     * @param array  $arParams Параметры.
     *
     * @return mixed
     * @throws GuzzleException
     */
    public static function apiCallHtml(string $url, array $arParams = [])
    {
        $client = new Client(['base_uri' => container()->getParameter('test.host.url')]);

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
     * @param array $arParams Параметры.
     *
     * @return string
     * @throws Exception
     */
    public static function getURLRouting(string $routeName, array $arParams = []) : string
    {
        if (!$routeName) {
            return '';
        }

        /** @var RouteCollection $router */
        $router = container()->get('symfony.get.routes');
        $context = new RequestContext();
        $generator = new UrlGenerator($router, $context);

        return $generator->generate($routeName, $arParams);
    }
}
