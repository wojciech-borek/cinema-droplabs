<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260117161517 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bookings (id INT AUTO_INCREMENT NOT NULL, customer_email VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, status VARCHAR(20) NOT NULL, screening_id INT NOT NULL, INDEX IDX_7A853C3570F5295D (screening_id), INDEX IDX_7A853C3529A7094F (customer_email), INDEX IDX_7A853C35F9D83E2 (expires_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE halls (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, is_active TINYINT DEFAULT 1 NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE screenings (id INT AUTO_INCREMENT NOT NULL, movie_title VARCHAR(255) NOT NULL, starts_at DATETIME NOT NULL, hall_id INT NOT NULL, INDEX IDX_350DCAA352AFCFD6 (hall_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE seat_allocations (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, expires_at DATETIME DEFAULT NULL, screening_id INT NOT NULL, seat_id INT NOT NULL, booking_id INT NOT NULL, INDEX IDX_E5B2F9F470F5295D (screening_id), INDEX IDX_E5B2F9F4C1DAFE35 (seat_id), INDEX IDX_E5B2F9F43301C60 (booking_id), INDEX IDX_E5B2F9F47B00651CF9D83E2 (status, expires_at), UNIQUE INDEX uniq_screening_seat (screening_id, seat_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE seats (id INT AUTO_INCREMENT NOT NULL, row_no INT NOT NULL, seat_no INT NOT NULL, hall_id INT NOT NULL, INDEX IDX_BFE2575052AFCFD6 (hall_id), UNIQUE INDEX uniq_hall_row_seat (hall_id, row_no, seat_no), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE bookings ADD CONSTRAINT FK_7A853C3570F5295D FOREIGN KEY (screening_id) REFERENCES screenings (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE screenings ADD CONSTRAINT FK_350DCAA352AFCFD6 FOREIGN KEY (hall_id) REFERENCES halls (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE seat_allocations ADD CONSTRAINT FK_E5B2F9F470F5295D FOREIGN KEY (screening_id) REFERENCES screenings (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE seat_allocations ADD CONSTRAINT FK_E5B2F9F4C1DAFE35 FOREIGN KEY (seat_id) REFERENCES seats (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE seat_allocations ADD CONSTRAINT FK_E5B2F9F43301C60 FOREIGN KEY (booking_id) REFERENCES bookings (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE seats ADD CONSTRAINT FK_BFE2575052AFCFD6 FOREIGN KEY (hall_id) REFERENCES halls (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bookings DROP FOREIGN KEY FK_7A853C3570F5295D');
        $this->addSql('ALTER TABLE screenings DROP FOREIGN KEY FK_350DCAA352AFCFD6');
        $this->addSql('ALTER TABLE seat_allocations DROP FOREIGN KEY FK_E5B2F9F470F5295D');
        $this->addSql('ALTER TABLE seat_allocations DROP FOREIGN KEY FK_E5B2F9F4C1DAFE35');
        $this->addSql('ALTER TABLE seat_allocations DROP FOREIGN KEY FK_E5B2F9F43301C60');
        $this->addSql('ALTER TABLE seats DROP FOREIGN KEY FK_BFE2575052AFCFD6');
        $this->addSql('DROP TABLE bookings');
        $this->addSql('DROP TABLE halls');
        $this->addSql('DROP TABLE screenings');
        $this->addSql('DROP TABLE seat_allocations');
        $this->addSql('DROP TABLE seats');
        $this->addSql('DROP TABLE users');
    }
}
