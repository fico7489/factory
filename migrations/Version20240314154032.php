<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240314154032 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adjustment (id INT AUTO_INCREMENT NOT NULL, discount_adjustment_id INT DEFAULT NULL, tax_adjustment_id INT DEFAULT NULL, order_id INT DEFAULT NULL, order_item_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, value DOUBLE PRECISION NOT NULL, INDEX IDX_89F97816F0226BB2 (discount_adjustment_id), INDEX IDX_89F97816A32988E8 (tax_adjustment_id), INDEX IDX_89F978168D9F6D38 (order_id), INDEX IDX_89F97816E415FB15 (order_item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_64C19C1727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contract_list (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, price DOUBLE PRECISION NOT NULL, sku VARCHAR(255) NOT NULL, INDEX IDX_2CBDB670A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discount_adjustment (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discount_item (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discount_item_adjustment (id INT AUTO_INCREMENT NOT NULL, amount DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, INDEX IDX_F5299398A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_item (id INT AUTO_INCREMENT NOT NULL, product_price_user_id INT DEFAULT NULL, order_id INT DEFAULT NULL, product_id INT DEFAULT NULL, quantity INT NOT NULL, price DOUBLE PRECISION DEFAULT NULL, price_adjusted DOUBLE PRECISION DEFAULT NULL, subtotal DOUBLE PRECISION DEFAULT NULL, discount_global DOUBLE PRECISION DEFAULT NULL, discount_item DOUBLE PRECISION DEFAULT NULL, tax DOUBLE PRECISION DEFAULT NULL, total DOUBLE PRECISION DEFAULT NULL, UNIQUE INDEX UNIQ_52EA1F0916A5073B (product_price_user_id), INDEX IDX_52EA1F098D9F6D38 (order_id), INDEX IDX_52EA1F094584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE price_item (id INT AUTO_INCREMENT NOT NULL, order_item_id INT DEFAULT NULL, price_list_id INT DEFAULT NULL, contract_list_id INT DEFAULT NULL, price DOUBLE PRECISION NOT NULL, type VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_6249D7A4E415FB15 (order_item_id), INDEX IDX_6249D7A45688DED7 (price_list_id), INDEX IDX_6249D7A435FF6B43 (contract_list_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE price_list (id INT AUTO_INCREMENT NOT NULL, user_group_id INT DEFAULT NULL, price DOUBLE PRECISION NOT NULL, sku VARCHAR(255) NOT NULL, INDEX IDX_399A0AA21ED93D47 (user_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, price DOUBLE PRECISION NOT NULL, sku VARCHAR(255) NOT NULL, published TINYINT(1) DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_category (product_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_CDFC73564584665A (product_id), INDEX IDX_CDFC735612469DE2 (category_id), PRIMARY KEY(product_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tax_adjustment (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, base DOUBLE PRECISION NOT NULL, rate DOUBLE PRECISION NOT NULL, value DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tax_item (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_user_group (user_id INT NOT NULL, user_group_id INT NOT NULL, INDEX IDX_28657971A76ED395 (user_id), INDEX IDX_286579711ED93D47 (user_group_id), PRIMARY KEY(user_id, user_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE adjustment ADD CONSTRAINT FK_89F97816F0226BB2 FOREIGN KEY (discount_adjustment_id) REFERENCES discount_adjustment (id)');
        $this->addSql('ALTER TABLE adjustment ADD CONSTRAINT FK_89F97816A32988E8 FOREIGN KEY (tax_adjustment_id) REFERENCES tax_adjustment (id)');
        $this->addSql('ALTER TABLE adjustment ADD CONSTRAINT FK_89F978168D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE adjustment ADD CONSTRAINT FK_89F97816E415FB15 FOREIGN KEY (order_item_id) REFERENCES order_item (id)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1727ACA70 FOREIGN KEY (parent_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE contract_list ADD CONSTRAINT FK_2CBDB670A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F0916A5073B FOREIGN KEY (product_price_user_id) REFERENCES price_item (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F094584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE price_item ADD CONSTRAINT FK_6249D7A4E415FB15 FOREIGN KEY (order_item_id) REFERENCES order_item (id)');
        $this->addSql('ALTER TABLE price_item ADD CONSTRAINT FK_6249D7A45688DED7 FOREIGN KEY (price_list_id) REFERENCES price_list (id)');
        $this->addSql('ALTER TABLE price_item ADD CONSTRAINT FK_6249D7A435FF6B43 FOREIGN KEY (contract_list_id) REFERENCES contract_list (id)');
        $this->addSql('ALTER TABLE price_list ADD CONSTRAINT FK_399A0AA21ED93D47 FOREIGN KEY (user_group_id) REFERENCES user_group (id)');
        $this->addSql('ALTER TABLE product_category ADD CONSTRAINT FK_CDFC73564584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_category ADD CONSTRAINT FK_CDFC735612469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_user_group ADD CONSTRAINT FK_28657971A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_user_group ADD CONSTRAINT FK_286579711ED93D47 FOREIGN KEY (user_group_id) REFERENCES user_group (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adjustment DROP FOREIGN KEY FK_89F97816F0226BB2');
        $this->addSql('ALTER TABLE adjustment DROP FOREIGN KEY FK_89F97816A32988E8');
        $this->addSql('ALTER TABLE adjustment DROP FOREIGN KEY FK_89F978168D9F6D38');
        $this->addSql('ALTER TABLE adjustment DROP FOREIGN KEY FK_89F97816E415FB15');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1727ACA70');
        $this->addSql('ALTER TABLE contract_list DROP FOREIGN KEY FK_2CBDB670A76ED395');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F0916A5073B');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F098D9F6D38');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F094584665A');
        $this->addSql('ALTER TABLE price_item DROP FOREIGN KEY FK_6249D7A4E415FB15');
        $this->addSql('ALTER TABLE price_item DROP FOREIGN KEY FK_6249D7A45688DED7');
        $this->addSql('ALTER TABLE price_item DROP FOREIGN KEY FK_6249D7A435FF6B43');
        $this->addSql('ALTER TABLE price_list DROP FOREIGN KEY FK_399A0AA21ED93D47');
        $this->addSql('ALTER TABLE product_category DROP FOREIGN KEY FK_CDFC73564584665A');
        $this->addSql('ALTER TABLE product_category DROP FOREIGN KEY FK_CDFC735612469DE2');
        $this->addSql('ALTER TABLE user_user_group DROP FOREIGN KEY FK_28657971A76ED395');
        $this->addSql('ALTER TABLE user_user_group DROP FOREIGN KEY FK_286579711ED93D47');
        $this->addSql('DROP TABLE adjustment');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE contract_list');
        $this->addSql('DROP TABLE discount_adjustment');
        $this->addSql('DROP TABLE discount_item');
        $this->addSql('DROP TABLE discount_item_adjustment');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_item');
        $this->addSql('DROP TABLE price_item');
        $this->addSql('DROP TABLE price_list');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_category');
        $this->addSql('DROP TABLE tax_adjustment');
        $this->addSql('DROP TABLE tax_item');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_user_group');
        $this->addSql('DROP TABLE user_group');
    }
}
