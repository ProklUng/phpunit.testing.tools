<?php

namespace Prokl\TestingTools\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use JsonException;

/**
 * Trait ResponseAsserts
 * Из Laravel.
 * @package Prokl\TestingTools\Traits
 * @see https://github.com/laravel/framework/blob/6.x/src/Illuminate/Foundation/Testing/TestResponse.php#L549
 *
 * @since 17.09.2020
 */
trait ResponseAsserts
{
    /**
     * Assert that the response contains the given JSON fragment.
     *
     * @param string $content Контент.
     * @param array  $data    Данные.
     *
     * @return $this
     */
    public function assertJsonFragment(string $content, array $data) : self
    {
        $actual = json_encode(Arr::sortRecursive(
            (array)$this->decodeResponseJson($content)
        ));

        foreach (Arr::sortRecursive($data) as $key => $value) {
            $expected = $this->jsonSearchStrings($key, $value);

            $this->assertTrue(
                Str::contains($actual, $expected),
                'Unable to find JSON fragment: '.PHP_EOL.PHP_EOL.
                '['.json_encode([$key => $value]).']'.PHP_EOL.PHP_EOL.
                'within'.PHP_EOL.PHP_EOL.
                "[{$actual}]."
            );
        }

        return $this;
    }

    /**
     * Assert that the response is a superset of the given JSON.
     *
     * @param mixed   $content Контент.
     * @param array   $data    Данные.
     * @param boolean $strict  Строгость.
     *
     * @return $this
     */
    public function assertJson($content, array $data, bool $strict = false): self
    {
        $this->assertArraySubset(
            $data,
            $this->decodeResponseJson($content),
            $strict,
            $this->assertJsonMessage($content, $data)
        );

        return $this;
    }

    /**
     * Assert that the response has the exact given JSON.
     *
     * @param mixed $content Контент.
     * @param array $data    Данные.
     *
     * @return $this
     * @throws JsonException
     */
    public function assertExactJson($content, array $data) : self
    {
        $actual = json_encode(Arr::sortRecursive(
            (array) $this->decodeResponseJson($content)
        ));

        $this->assertEquals(
            json_encode(Arr::sortRecursive($data)),
            $actual
        );

        return $this;
    }

    /**
     * Assert that the response does not contain the given JSON fragment.
     *
     * @param mixed   $content Контент.
     * @param array   $data    Данные.
     * @param boolean $exact   Точность.
     *
     * @return $this
     */
    public function assertJsonMissing($content, array $data, bool $exact = false): self
    {
        if ($exact) {
            return $this->assertJsonMissingExact($content, $data);
        }

        $actual = json_encode(Arr::sortRecursive(
            (array)$this->decodeResponseJson($content)
        ));

        foreach (Arr::sortRecursive($data) as $key => $value) {
            $unexpected = $this->jsonSearchStrings($key, $value);

            $this->assertFalse(
                Str::contains($actual, $unexpected),
                'Found unexpected JSON fragment: '.PHP_EOL.PHP_EOL.
                '['.json_encode([$key => $value]).']'.PHP_EOL.PHP_EOL.
                'within'.PHP_EOL.PHP_EOL.
                "[{$actual}]."
            );
        }

        return $this;
    }


    /**
     * Assert that the response does not contain the exact JSON fragment.
     *
     * @param mixed $content Контент.
     * @param array $data    Данные.
     *
     * @return $this
     */
    public function assertJsonMissingExact($content, array $data) : self
    {
        $actual = json_encode(Arr::sortRecursive(
            (array)$this->decodeResponseJson($content)
        ));

        foreach (Arr::sortRecursive($data) as $key => $value) {
            $unexpected = $this->jsonSearchStrings($key, $value);

            if (! Str::contains($actual, $unexpected)) {
                return $this;
            }
        }

        $this->assertTrue(
            false,
            'Found unexpected JSON fragment: '.PHP_EOL.PHP_EOL.
            '['.json_encode($data).']'.PHP_EOL.PHP_EOL.
            'within'.PHP_EOL.PHP_EOL.
            "[{$actual}]."
        );

        return $this;
    }

    /**
     * Assert that the given string is contained within the response text.
     *
     * @param mixed  $content Контент.
     * @param string $value   Значение.
     *
     * @return $this
     */
    public function assertSeeText($content, string $value): self
    {
        $this->assertStringContainsString(
            $value,
            strip_tags($content)
        );

        return $this;
    }

    /**
     * Assert that the given string is contained within the response text.
     *
     * @param mixed  $content
     * @param string $value
     *
     * @return $this
     */
    public function assertDontSee($content, string $value): self
    {
        $this->assertStringNotContainsString(
            $value,
            strip_tags($content)
        );

        return $this;
    }

    /**
     * Validate and return the decoded response JSON.
     *
     * @param mixed       $content Контент.
     * @param string|null $key     Ключ.
     *
     * @return mixed
     */
    public function decodeResponseJson($content, ?string $key = null)
    {
        $decodedResponse = json_decode($content, true, 512);

        if ($decodedResponse === false || is_null($decodedResponse)) {
            return null;
        }

        return data_get($decodedResponse, $key);
    }

    /**
     * Get the strings we need to search for when examining the JSON.
     *
     * @param  string $key
     * @param  string $value
     * @return array
     */
    protected function jsonSearchStrings(string $key, string $value): array
    {
        $needle = substr(json_encode([$key => $value]), 1, -1);

        return [
            $needle.']',
            $needle.'}',
            $needle.',',
        ];
    }

    /**
     * Get the assertion message for assertJson.
     *
     * @param mixed $content
     * @param array $data
     *
     * @return string
     * @throws JsonException
     */
    protected function assertJsonMessage($content, array $data): string
    {
        $expected = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $actual = json_encode($this->decodeResponseJson($content), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return 'Unable to find JSON: '.PHP_EOL.PHP_EOL.
            "[{$expected}]".PHP_EOL.PHP_EOL.
            'within response JSON:'.PHP_EOL.PHP_EOL.
            "[{$actual}].".PHP_EOL.PHP_EOL;
    }
}
