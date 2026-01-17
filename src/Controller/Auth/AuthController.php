<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Application\Command\LoginUser\LoginUserCommand;
use App\Bus\Interface\CommandBusInterface;
use App\DTO\Request\LoginRequest;
use App\DTO\Response\AuthResponse;
use App\Exception\InvalidCredentialsException;
use App\ValueObject\EmailAddress;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/auth', name: 'api_v1_auth_')]
final class AuthController extends AbstractController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(
        #[MapRequestPayload]
        LoginRequest $request
    ): JsonResponse {
        try {
            $command = new LoginUserCommand(
                email: EmailAddress::fromString($request->email),
                password: $request->password,
            );

            /** @var AuthResponse $response */
            $response = $this->commandBus->dispatch($command);

            return $this->json(data: $response, status: Response::HTTP_OK);
        } catch (HandlerFailedException $e) {
            $originalException = $e->getPrevious();

            if ($originalException instanceof InvalidCredentialsException) {
                throw $originalException;
            }

            throw $e;
        }
    }
}
