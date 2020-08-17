<?php
/**
 * Copyright (c) Diffco US, Inc
 */

declare(strict_types=1);

namespace App\Tests\Controller\Api\V1;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ArticleControllerTest extends WebTestCase
{
    protected KernelBrowser $client;

    protected string $token;

    /**
     * @throws \JsonException
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->token = $this->login();
    }

    /**
     * @throws \JsonException
     */
    public function testGetArticleList(): void
    {
        $this->client->request('GET', '/api/v1/articles');
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $response = $this->client->getResponse()->getContent();
        $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($data);
    }

    /**
     * @throws \JsonException
     */
    public function testGetArticle(): void
    {
        $testArticle = $this->createArticle('Test article', 'qwerty');
        $this->client->request('GET', '/api/v1/articles/'.$testArticle['id']);
        $response = $this->client->getResponse()->getContent();
        $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertIsArray($data);
        self::assertIsNumeric($data['id']);
        self::assertSame('Test article', $data['title']);
        self::assertSame('qwerty', $data['body']);
    }

    /**
     * @throws \JsonException
     */
    public function testPostArticle(): void
    {
        $this->client->request(
            'POST',
            '/api/v1/articles',
            [],
            [],
            ['HTTP_Authorization' => 'Bearer '.$this->token],
            json_encode(
                [
                    'title' => 'test',
                    'body' => 'blah-blah-blah',
                ],
                JSON_THROW_ON_ERROR
            )
        );
        $response = $this->client->getResponse()->getContent();
        $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertIsArray($data);
        self::assertIsNumeric($data['id']);
        self::assertSame('test', $data['title']);
        self::assertSame('blah-blah-blah', $data['body']);
    }

    /**
     * @throws \JsonException
     */
    public function testPutArticle(): void
    {
        $testArticle = $this->createArticle('Test article', 'qwerty');
        $this->client->request(
            'PUT',
            '/api/v1/articles/'.$testArticle['id'],
            [],
            [],
            ['HTTP_Authorization' => 'Bearer '.$this->token],
            json_encode(
                [
                    'title' => 'test',
                    'body' => 'blah-blah-blah',
                ],
                JSON_THROW_ON_ERROR
            )
        );
        $response = $this->client->getResponse()->getContent();
        $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertIsArray($data);
        self::assertIsNumeric($data['id']);
        self::assertSame('test', $data['title']);
        self::assertSame('blah-blah-blah', $data['body']);
    }

    /**
     * @throws \JsonException
     */
    public function testDeleteArticle(): void
    {
        $testArticle = $this->createArticle('Test article', 'qwerty');
        $this->client->request(
            'DELETE',
            '/api/v1/articles/'.$testArticle['id'],
            [],
            [],
            ['HTTP_Authorization' => 'Bearer '.$this->token]
        );
        $response = $this->client->getResponse()->getContent();
        self::assertEquals(204, $this->client->getResponse()->getStatusCode());
        $this->client->request('GET', '/api/v1/articles/'.$testArticle['id']);
        self::assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @throws \JsonException
     */
    private function login(): string
    {
        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            [],
            json_encode(
                [
                    'username' => 'test_user',
                    'password' => 'qwerty',
                ],
                JSON_THROW_ON_ERROR
            )
        );
        $response = $this->client->getResponse()->getContent();
        $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        return $data['token'];
    }

    /**
     * @throws \JsonException
     */
    private function createArticle(string $title, string $body): array
    {
        $this->client->request(
            'POST',
            '/api/v1/articles',
            [],
            [],
            ['HTTP_Authorization' => 'Bearer '.$this->token],
            json_encode(
                [
                    'title' => $title,
                    'body' => $body,
                ],
                JSON_THROW_ON_ERROR
            )
        );
        $response = $this->client->getResponse()->getContent();

        return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    }
}
