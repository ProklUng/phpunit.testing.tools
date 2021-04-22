<?php

namespace Prokl\TestingTools\Tools\Macros;

use Prokl\TestingTools\Tools\TestResponseMacros;

/**
 * Class MacrosInit
 * @package Prokl\TestingTools\Tools\Macros
 *
 * @since 18.09.2020
 * @since 20.09.2020 Новые макросы.
 */
class MacrosInit
{
    /** @var array $macroses Макросы. */
    private $macroses;

    /**
     * MacrosInit constructor.
     */
    public function __construct()
    {
        // Тут задаются макросы. string -> метод в том же классе. [класс, метод] - в другом.
        $this->macroses = [
            [TestResponseMacros::class, 'assertErrorInResponse'],
            [TestResponseMacros::class, 'assertOkResponse'],
        ];
    }

    /**
     * Инициализация.
     *
     * @return void
     */
    public function init() : void
    {

        foreach ($this->macroses as $macros) {
            if (is_array($macros)) {
                $object = new $macros[0];
                $method = $macros[1];
                $object->{$method}();
                continue;
            }

            if (method_exists($this, $macros)) {
                $this->{$macros}();
            }
        }
    }
}
