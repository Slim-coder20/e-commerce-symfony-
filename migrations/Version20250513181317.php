<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250513181317 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE product_subcategory (product_id INT NOT NULL, subcategory_id INT NOT NULL, INDEX IDX_A1F33A574584665A (product_id), INDEX IDX_A1F33A575DC6FE57 (subcategory_id), PRIMARY KEY(product_id, subcategory_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_subcategory ADD CONSTRAINT FK_A1F33A574584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_subcategory ADD CONSTRAINT FK_A1F33A575DC6FE57 FOREIGN KEY (subcategory_id) REFERENCES subcategory (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE product_subcategory DROP FOREIGN KEY FK_A1F33A574584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_subcategory DROP FOREIGN KEY FK_A1F33A575DC6FE57
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product_subcategory
        SQL);
    }
}
