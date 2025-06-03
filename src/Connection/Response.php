<?php

declare(strict_types=1);

namespace SherinBloemendaal\PhpIPAMClient\Connection;

class Response
{
    protected int $code;
    protected ?bool $success;
    protected ?string $message;
    protected mixed $data;
    protected float $time;

    protected array $body;

    public function __construct(\GuzzleHttp\Psr7\Response $response)
    {
        // Get Body from guzzle response
        $body = json_decode((string) $response->getBody(), true);
        $this->body = $body;

        $this->code = $body['code'];

        if (isset($body['success'])) {
            $this->success = (bool) $body['success'];
        }

        if (isset($body['message'])) {
            $this->message = $body['message'];
        }

        if (isset($body['data'])) {
            $this->data = $body['data'];
        }

        $this->time = $body['time'];
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @return string|array|null
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    public function getTime(): float
    {
        return $this->time;
    }

    /**
     * @return mixed
     */
    public function getBody(): array
    {
        return $this->body;
    }
}
