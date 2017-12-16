<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171215211808 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE User (id VARCHAR(255) NOT NULL, auth_token VARCHAR(255) DEFAULT NULL, auth_token_expire DATETIME DEFAULT NULL, display_name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, facebook_user_id VARCHAR(255) DEFAULT NULL, google_user_id VARCHAR(255) DEFAULT NULL, slack_user_id VARCHAR(255) DEFAULT NULL, forget_password_token VARCHAR(255) DEFAULT NULL, forget_password_expired DATETIME DEFAULT NULL, image_url VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json_array)\', bio LONGTEXT DEFAULT NULL, enabled TINYINT(1) NOT NULL, source VARCHAR(255) NOT NULL, refresh_token VARCHAR(255) DEFAULT NULL, refresh_token_expire DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_2DA17977D5499347 (display_name), UNIQUE INDEX UNIQ_2DA17977E7927C74 (email), UNIQUE INDEX UNIQ_2DA17977D155CFEE (facebook_user_id), UNIQUE INDEX UNIQ_2DA17977592AEE13 (google_user_id), UNIQUE INDEX UNIQ_2DA17977E6AA7332 (slack_user_id), INDEX idk_email (email), INDEX idk_google_user_id (google_user_id), INDEX idk_slack_user_Id (slack_user_id), INDEX idk_facebook_user_id (facebook_user_id), INDEX idk_forget_password_token (forget_password_token), INDEX idk_refresh_token (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE User');
    }
}
