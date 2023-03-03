<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230227110202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users ADD categorie_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9F8ED7D37 FOREIGN KEY (categorie_user_id) REFERENCES categorie_user (id)');
        $this->addSql('CREATE INDEX IDX_1483A5E9F8ED7D37 ON users (categorie_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9F8ED7D37');
        $this->addSql('DROP INDEX IDX_1483A5E9F8ED7D37 ON users');
        $this->addSql('ALTER TABLE users DROP categorie_user_id');
    }
}
