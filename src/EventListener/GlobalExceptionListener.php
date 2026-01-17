<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Exception\BookingExpiredException;
use App\Exception\EntityNotFoundException;
use App\Exception\InvalidCredentialsException;
use App\Exception\InvalidSeatsException;
use App\Exception\ScreeningStartedException;
use App\Exception\SeatsUnavailableException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[AsEventListener(event: 'kernel.exception', method: 'onKernelException')]
final readonly class GlobalExceptionListener
{
    public function __construct(
        private string $environment,
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Unwrap HandlerFailedException from Symfony Messenger
        if ($exception instanceof HandlerFailedException) {
            $wrapped = $exception->getWrappedExceptions();
            if (count($wrapped) > 0) {
                $wrapped = array_values($wrapped);
                $exception = $wrapped[0];
            }
        }

        $response = match (true) {
            $exception instanceof EntityNotFoundException => $this->handleEntityNotFoundException($exception),
            $exception instanceof InvalidCredentialsException => $this->handleInvalidCredentialsException($exception),
            $exception instanceof SeatsUnavailableException => $this->handleSeatsUnavailableException($exception),
            $exception instanceof InvalidSeatsException => $this->handleInvalidSeatsException($exception),
            $exception instanceof BookingExpiredException => $this->handleBookingExpiredException($exception),
            $exception instanceof ScreeningStartedException => $this->handleScreeningStartedException($exception),
            $exception instanceof ValidationFailedException => $this->handleValidationException($exception),
            $exception instanceof UnprocessableEntityHttpException => $this->handleUnprocessableEntityException($exception),
            $exception instanceof HttpExceptionInterface => $this->handleHttpException($exception),
            default => $this->handleGenericException($exception),
        };

        $event->setResponse($response);
    }

    private function handleEntityNotFoundException(EntityNotFoundException $exception): JsonResponse
    {
        return $this->errorResponse(
            type: 'entity_not_found',
            message: $exception->getMessage(),
            code: Response::HTTP_NOT_FOUND,
            details: [
                'entity' => $exception->getEntityType(),
                'id' => $exception->getEntityId(),
            ]
        );
    }

    private function handleValidationException(ValidationFailedException $exception): JsonResponse
    {
        $violations = [];
        foreach ($exception->getViolations() as $violation) {
            $violations[] = [
                'field' => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
            ];
        }

        return $this->errorResponse(
            type: 'validation_failed',
            message: 'Validation failed',
            code: Response::HTTP_UNPROCESSABLE_ENTITY,
            details: [
                'violations' => $violations,
            ]
        );
    }

    private function handleUnprocessableEntityException(UnprocessableEntityHttpException $exception): JsonResponse
    {
        $previous = $exception->getPrevious();

        if ($previous instanceof ValidationFailedException) {
            return $this->handleValidationException($previous);
        }

        return $this->handleHttpException($exception);
    }

    private function handleHttpException(HttpExceptionInterface $exception): JsonResponse
    {
        return $this->errorResponse(
            type: 'http_error',
            message: $exception->getMessage(),
            code: $exception->getStatusCode()
        );
    }

    private function handleGenericException(\Throwable $exception): JsonResponse
    {
        $message = 'dev' === $this->environment
            ? $exception->getMessage()
            : 'An error occurred while processing your request';

        return $this->errorResponse(
            type: 'internal_error',
            message: $message,
            code: Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    private function handleInvalidCredentialsException(InvalidCredentialsException $exception): JsonResponse
    {
        return $this->errorResponse(
            type: 'invalid_credentials',
            message: $exception->getMessage(),
            code: Response::HTTP_UNAUTHORIZED
        );
    }

    private function handleSeatsUnavailableException(SeatsUnavailableException $exception): JsonResponse
    {
        return $this->errorResponse(
            type: 'seats_unavailable',
            message: $exception->getMessage(),
            code: Response::HTTP_CONFLICT,
            details: [
                'unavailable_seats' => $exception->getUnavailableSeatIds(),
            ]
        );
    }

    private function handleInvalidSeatsException(InvalidSeatsException $exception): JsonResponse
    {
        return $this->errorResponse(
            type: 'invalid_seats',
            message: $exception->getMessage(),
            code: Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    private function handleBookingExpiredException(BookingExpiredException $exception): JsonResponse
    {
        return $this->errorResponse(
            type: 'booking_expired',
            message: $exception->getMessage(),
            code: Response::HTTP_GONE
        );
    }

    private function handleScreeningStartedException(ScreeningStartedException $exception): JsonResponse
    {
        return $this->errorResponse(
            type: 'screening_started',
            message: $exception->getMessage(),
            code: Response::HTTP_CONFLICT,
            details: [
                'screening_id' => $exception->getScreeningId(),
            ]
        );
    }

    /**
     * @param array<string, mixed>|null $details
     */
    private function errorResponse(string $type, string $message, int $code, ?array $details = null): JsonResponse
    {
        $error = [
            'type' => $type,
            'message' => $message,
            'code' => $code,
        ];

        if (null !== $details) {
            $error['details'] = $details;
        }

        return new JsonResponse(
            data: ['error' => $error],
            status: $code
        );
    }
}
