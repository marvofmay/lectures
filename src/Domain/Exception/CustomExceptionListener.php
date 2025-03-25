<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\Exception;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class CustomExceptionListener
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpExceptionInterface) {
            $message = $exception->getMessage();
        } else {
            $message = 'message.unexpectedError';
        }

        $response = new JsonResponse([
            'message' => $this->translator->trans($message, [], 'lectures'),
        ], Response::HTTP_INTERNAL_SERVER_ERROR);

        $event->setResponse($response);
    }
}