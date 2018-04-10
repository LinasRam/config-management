<?php declare(strict_types = 1);

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180410185859 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE configuration (id INT AUTO_INCREMENT NOT NULL, config_group_id INT DEFAULT NULL, `key` VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, INDEX IDX_A5E2A5D7439C3799 (config_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE config_group (id INT AUTO_INCREMENT NOT NULL, application_id INT DEFAULT NULL, environment_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_D0ED16853E030ACD (application_id), INDEX IDX_D0ED1685903E3A94 (environment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE config_group_hierarchy (parent_config_group_id INT NOT NULL, child_config_group_id INT NOT NULL, INDEX IDX_DB0643F8D4503365 (parent_config_group_id), INDEX IDX_DB0643F8770054B4 (child_config_group_id), PRIMARY KEY(parent_config_group_id, child_config_group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE configuration ADD CONSTRAINT FK_A5E2A5D7439C3799 FOREIGN KEY (config_group_id) REFERENCES config_group (id)');
        $this->addSql('ALTER TABLE config_group ADD CONSTRAINT FK_D0ED16853E030ACD FOREIGN KEY (application_id) REFERENCES application (id)');
        $this->addSql('ALTER TABLE config_group ADD CONSTRAINT FK_D0ED1685903E3A94 FOREIGN KEY (environment_id) REFERENCES environment (id)');
        $this->addSql('ALTER TABLE config_group_hierarchy ADD CONSTRAINT FK_DB0643F8D4503365 FOREIGN KEY (parent_config_group_id) REFERENCES config_group (id)');
        $this->addSql('ALTER TABLE config_group_hierarchy ADD CONSTRAINT FK_DB0643F8770054B4 FOREIGN KEY (child_config_group_id) REFERENCES config_group (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE configuration DROP FOREIGN KEY FK_A5E2A5D7439C3799');
        $this->addSql('ALTER TABLE config_group_hierarchy DROP FOREIGN KEY FK_DB0643F8D4503365');
        $this->addSql('ALTER TABLE config_group_hierarchy DROP FOREIGN KEY FK_DB0643F8770054B4');
        $this->addSql('DROP TABLE configuration');
        $this->addSql('DROP TABLE config_group');
        $this->addSql('DROP TABLE config_group_hierarchy');
    }
}
