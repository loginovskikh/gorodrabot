<?php

namespace AddressFinder\ErrorRenderer;

class JsonErrorRenderer extends \Slim\Error\Renderers\JsonErrorRenderer
{

    public function __invoke(\Throwable $exception, bool $displayErrorDetails): string
    {
        if ($exception instanceof \DomainException) {
            $error['message'] = $exception->getMessage();
        } else {
            $error = ['message' => $this->getErrorTitle($exception)];
        }

        if ($displayErrorDetails) {
            $error['exception'] = [];
            do {
                $error['exception'][] = $this->formatExceptionFragment($exception);
            } while ($exception = $exception->getPrevious());
        }

        return (string) json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private function formatExceptionFragment(\Throwable $exception): array
    {
        /** @var int|string $code */
        $code = $exception->getCode();
        return [
            'type' => get_class($exception),
            'code' => $code,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ];
    }

}