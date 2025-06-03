<?php

declare(strict_types=1);

namespace SherinBloemendaal\PhpIPAMClient\Exception;

use SherinBloemendaal\PhpIPAMClient\Connection\Response;

class PhpIPAMRequestException extends PhpIPAMException
{
    protected Response $response;

    public function __construct(Response $response, string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        $this->response = $response;
        parent::__construct($message, $code, $previous);
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}
