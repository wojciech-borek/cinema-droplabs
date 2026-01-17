# Cinema Booking System

System rezerwacji miejsc w kinie oparty na Symfony 8.0 i PHP 8.4.

## Wymagania

- PHP >= 8.4
- MySQL/MariaDB
- Docker & Docker Compose (dla lokalnego uruchomienia)

## Uruchomienie

```bash
# Sklonuj repozytorium
git clone <repository-url>
cd cinema-droplabs

# Uruchom aplikację
docker-compose up -d

# Zainstaluj zależności
docker-compose exec php composer install

# Wykonaj migracje bazy danych
docker-compose exec php php bin/console doctrine:migrations:migrate

# Załaduj dane testowe
docker-compose exec php php bin/console doctrine:fixtures:load

# Uruchom testy
docker-compose exec php php bin/console test
```

## Dostęp do aplikacji

- **API**: http://localhost:8080
- **Swagger UI**: http://localhost:8081

## Dane testowe

### Konto administratora
- Email: `admin@cinema.com`
- Hasło: `admin`

## Endpointy API

### Publiczne
- `GET /api/v1/screenings/{id}/seats` - Pobierz dostępne miejsca na seans
- `POST /api/v1/bookings` - Utwórz rezerwację

### Administracyjne (wymaga JWT token)
- `POST /api/v1/admin/halls` - Utwórz nową salę
- `GET /api/v1/admin/halls` - Lista sal
- `GET /api/v1/admin/halls/{id}` - Szczegóły sali
- `PUT /api/v1/admin/halls/{id}` - Aktualizuj salę
- `DELETE /api/v1/admin/halls/{id}` - Usuń salę

### Autentyfikacja
- `POST /api/v1/auth/login` - Logowanie administratora

## Architektura

Projekt wykorzystuje:
- **CQRS Pattern** z Command Bus i Query Bus
- **Value Objects** dla EmailAddress i MovieTitle
- **Domain Exceptions** dla błędów biznesowych
- **DTO** dla request/response
- **Doctrine ORM** dla warstwy danych
- **JWT Authentication** dla zabezpieczenia endpointów admin
- **Symfony Messenger** dla obsługi komend i zapytań

## Testy

```bash
# Wszystkie testy
composer run test

# Testy z pokryciem
vendor/bin/phpunit --coverage-html=reports/coverage

# Analiza statyczna kodu
composer run phpstan

# Sprawdzanie stylu kodu
composer run cs-check
```

## Struktura bazy danych

- `halls` - Sale kinowe
- `seats` - Miejsca w salach
- `screenings` - Seanse filmowe
- `bookings` - Rezerwacje
- `seat_allocations` - Alokacje miejsc
- `users` - Użytkownicy (administratorzy)

## Status rezerwacji

- `HELD` - Rezerwacja tymczasowa (15 min ważności)
- `CONFIRMED` - Rezerwacja potwierdzona
- `CANCELLED` - Rezerwacja anulowana

## Walidacja

- Email musi być prawidłowym adresem email
- Miejsca muszą być dostępne w momencie rezerwacji
- Seans nie może się rozpocząć przed dokonaniem rezerwacji
- Minimalna liczba miejsc w rezerwacji: 1