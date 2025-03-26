<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\Exception;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CustomExceptionListener
{
    public function __construct(private TranslatorInterface $translator, private LoggerInterface $logger)
    {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpExceptionInterface) {
            $message = $exception->getMessage();
        } else {
            $message = 'message.unexpectedError';
            $this->logger->error($message, ['exception' => $exception]);
        }

        $response = new JsonResponse([
            'message' => $this->translator->trans($message, [], 'lectures'),
        ], Response::HTTP_INTERNAL_SERVER_ERROR);

        $event->setResponse($response);
    }
}
