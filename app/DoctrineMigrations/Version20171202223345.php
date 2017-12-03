<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171202223345 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE INDEX idk_email ON User (email)');
        $this->addSql('CREATE INDEX idk_google_user_id ON User (google_user_id)');
        $this->addSql('CREATE INDEX idk_slack_user_Id ON User (slack_user_id)');
        $this->addSql('CREATE INDEX idk_facebook_user_id ON User (facebook_user_id)');
        $this->addSql('CREATE INDEX idk_forget_password_token ON User (forget_password_token)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX idk_email ON User');
        $this->addSql('DROP INDEX idk_google_user_id ON User');
        $this->addSql('DROP INDEX idk_slack_user_Id ON User');
        $this->addSql('DROP INDEX idk_facebook_user_id ON User');
        $this->addSql('DROP INDEX idk_forget_password_token ON User');
    }
}
