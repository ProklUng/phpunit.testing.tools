<?php

namespace Prokl\TestingTools\Traits;

use Exception;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HttpAssertionsTrait
 * @package Tests
 */
trait HttpAssertionsTrait
{
    /**
     * Checks the success state of a response.
     *
     * @param Response $response Response object
     * @param bool     $success  to define whether the response is expected to be successful
     * @param string   $type
     */
    public function isSuccessful(Response $response, bool $success = true, string $type = 'text/html'): void
    {
        try {
            $crawler = new Crawler();
            $crawler->addContent($response->getContent(), $type);
            if (!count($crawler->filter('title'))) {
                $title = '['.$response->getStatusCode().'] - '.$response->getContent();
            } else {
                $title = $crawler->filter('title')->text();
            }
        } catch (Exception $e) {
            $title = $e->getMessage();
        }

        if ($success) {
            $this->assertTrue(
                $response->isSuccessful(),
                'The Response was not successful: '. $title
            );
        } else {
            $this->assertFalse(
                $response->isSuccessful(),
                'The Response was successful: '. $title
            );
        }
    }
}
