<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260305161835 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cotation (id INT AUTO_INCREMENT NOT NULL, price NUMERIC(15, 2) NOT NULL, quoted_at DATETIME NOT NULL, cryptocurrency_id INT NOT NULL, INDEX IDX_996DA944583FC03A (cryptocurrency_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE cryptocurrency (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, symbol VARCHAR(10) NOT NULL, UNIQUE INDEX UNIQ_CC62CFADECC836F9 (symbol), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE purchase (id INT AUTO_INCREMENT NOT NULL, quantity NUMERIC(18, 8) NOT NULL, price_at_purchase NUMERIC(15, 2) NOT NULL, purchased_at DATETIME NOT NULL, user_id INT NOT NULL, cryptocurrency_id INT NOT NULL, INDEX IDX_6117D13BA76ED395 (user_id), INDEX IDX_6117D13B583FC03A (cryptocurrency_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(100) NOT NULL, lastname VARCHAR(100) NOT NULL, balance NUMERIC(15, 2) DEFAULT 0 NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE cotation ADD CONSTRAINT FK_996DA944583FC03A FOREIGN KEY (cryptocurrency_id) REFERENCES cryptocurrency (id)');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13BA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B583FC03A FOREIGN KEY (cryptocurrency_id) REFERENCES cryptocurrency (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cotation DROP FOREIGN KEY FK_996DA944583FC03A');
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13BA76ED395');
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B583FC03A');
        $this->addSql('DROP TABLE cotation');
        $this->addSql('DROP TABLE cryptocurrency');
        $this->addSql('DROP TABLE purchase');
        $this->addSql('DROP TABLE `user`');
    }
}
