<?php

declare(strict_types=1);

namespace App\Application\Command\LoginUser;

use App\DTO\Response\AuthResponse;
use App\Exception\InvalidCredentialsException;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler(bus: 'command_bus')]
final readonly class LoginUserHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private JWTTokenManagerInterface $jwtManager,
    ) {
    }

    public function __invoke(LoginUserCommand $command): AuthResponse
    {
        $user = $this->userRepository->findByEmail($command->email->toString());

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $command->password)) {
            throw InvalidCredentialsException::create();
        }

        $token = $this->jwtManager->create($user);

        return new AuthResponse(
            token: $token,
            expiresIn: 3600,
        );
    }
}
