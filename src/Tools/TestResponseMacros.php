<?php

namespace Prokl\TestingTools\Tools;

/**
 * Class TestResponseMacros
 * @package Prokl\TestingTools\Tools
 *
 * @since 18.09.2020
 * @since 20.09.2020 Расширение функционала.
 */
class TestResponseMacros
{
    /**
     * Ошибка в ответе.
     *
     * @return void
     */
    public function assertErrorInResponse() : void
    {
        TestResponse::macro('assertErrorInResponse', function () {
            /** @var $this TestResponse */
            return $this->assertNotSuccessful()
                        ->assertStatus(400)
                        ->assertJsonFragment(['error' => true]);
        });
    }

    /**
     * Ошибка в ответе.
     *
     * @return void
     *
     * @since 20.09.2020
     */
    public function assertOkResponse() : void
    {
        TestResponse::macro('assertOkResponse', function () {
            /** @var $this TestResponse */
            return $this->assertSuccessful()
                ->assertStatus(200)
                ->assertJsonFragment(['error' => false]);
        });
    }
}
