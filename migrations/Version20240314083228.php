<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240314083228 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adjustment (id INT AUTO_INCREMENT NOT NULL, price_adjustment_id INT DEFAULT NULL, discount_adjustment_id INT DEFAULT NULL, tax_adjustment_id INT DEFAULT NULL, order_id INT DEFAULT NULL, order_item_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, value DOUBLE PRECISION NOT NULL, INDEX IDX_89F978163E9F1C88 (price_adjustment_id), INDEX IDX_89F97816F0226BB2 (discount_adjustment_id), INDEX IDX_89F97816A32988E8 (tax_adjustment_id), INDEX IDX_89F978168D9F6D38 (order_id), INDEX IDX_89F97816E415FB15 (order_item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contract_list (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, price DOUBLE PRECISION NOT NULL, sku VARCHAR(255) NOT NULL, INDEX IDX_2CBDB670A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discount_adjustment (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, INDEX IDX_F5299398A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_item (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, product_id INT DEFAULT NULL, quantity INT NOT NULL, price DOUBLE PRECISION NOT NULL, price_adjusted DOUBLE PRECISION NOT NULL, discount DOUBLE PRECISION NOT NULL, tax DOUBLE PRECISION NOT NULL, total DOUBLE PRECISION NOT NULL, INDEX IDX_52EA1F098D9F6D38 (order_id), INDEX IDX_52EA1F094584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE price_adjustment (id INT AUTO_INCREMENT NOT NULL, price_list_id INT DEFAULT NULL, contract_list_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_3D7177125688DED7 (price_list_id), INDEX IDX_3D71771235FF6B43 (contract_list_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE price_list (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, sku VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tax_adjustment (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, base DOUBLE PRECISION NOT NULL, rate DOUBLE PRECISION NOT NULL, value DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE adjustment ADD CONSTRAINT FK_89F978163E9F1C88 FOREIGN KEY (price_adjustment_id) REFERENCES price_adjustment (id)');
        $this->addSql('ALTER TABLE adjustment ADD CONSTRAINT FK_89F97816F0226BB2 FOREIGN KEY (discount_adjustment_id) REFERENCES discount_adjustment (id)');
        $this->addSql('ALTER TABLE adjustment ADD CONSTRAINT FK_89F97816A32988E8 FOREIGN KEY (tax_adjustment_id) REFERENCES tax_adjustment (id)');
        $this->addSql('ALTER TABLE adjustment ADD CONSTRAINT FK_89F978168D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE adjustment ADD CONSTRAINT FK_89F97816E415FB15 FOREIGN KEY (order_item_id) REFERENCES order_item (id)');
        $this->addSql('ALTER TABLE contract_list ADD CONSTRAINT FK_2CBDB670A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F094584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE price_adjustment ADD CONSTRAINT FK_3D7177125688DED7 FOREIGN KEY (price_list_id) REFERENCES price_list (id)');
        $this->addSql('ALTER TABLE price_adjustment ADD CONSTRAINT FK_3D71771235FF6B43 FOREIGN KEY (contract_list_id) REFERENCES contract_list (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adjustment DROP FOREIGN KEY FK_89F978163E9F1C88');
        $this->addSql('ALTER TABLE adjustment DROP FOREIGN KEY FK_89F97816F0226BB2');
        $this->addSql('ALTER TABLE adjustment DROP FOREIGN KEY FK_89F97816A32988E8');
        $this->addSql('ALTER TABLE adjustment DROP FOREIGN KEY FK_89F978168D9F6D38');
        $this->addSql('ALTER TABLE adjustment DROP FOREIGN KEY FK_89F97816E415FB15');
        $this->addSql('ALTER TABLE contract_list DROP FOREIGN KEY FK_2CBDB670A76ED395');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F098D9F6D38');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F094584665A');
        $this->addSql('ALTER TABLE price_adjustment DROP FOREIGN KEY FK_3D7177125688DED7');
        $this->addSql('ALTER TABLE price_adjustment DROP FOREIGN KEY FK_3D71771235FF6B43');
        $this->addSql('DROP TABLE adjustment');
        $this->addSql('DROP TABLE contract_list');
        $this->addSql('DROP TABLE discount_adjustment');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_item');
        $this->addSql('DROP TABLE price_adjustment');
        $this->addSql('DROP TABLE price_list');
        $this->addSql('DROP TABLE tax_adjustment');
        $this->addSql('DROP TABLE user');
    }
}
