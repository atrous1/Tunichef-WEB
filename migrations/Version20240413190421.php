<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240413190421 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC7305E0476');
        $this->addSql('DROP INDEX IDX_5FB6DEC7305E0476 ON reponse');
        $this->addSql('ALTER TABLE reponse ADD reclamation_id INT NOT NULL, DROP id_rec_id');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC72D6BA2D9 FOREIGN KEY (reclamation_id) REFERENCES reclamation (id)');
        $this->addSql('CREATE INDEX IDX_5FB6DEC72D6BA2D9 ON reponse (reclamation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC72D6BA2D9');
        $this->addSql('DROP INDEX IDX_5FB6DEC72D6BA2D9 ON reponse');
        $this->addSql('ALTER TABLE reponse ADD id_rec_id INT DEFAULT NULL, DROP reclamation_id');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC7305E0476 FOREIGN KEY (id_rec_id) REFERENCES reclamation (id)');
        $this->addSql('CREATE INDEX IDX_5FB6DEC7305E0476 ON reponse (id_rec_id)');
    }
}
