<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261104113729 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE administrador (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE afectado (id INT AUTO_INCREMENT NOT NULL, fecha DATETIME NOT NULL, incidencia_id INT NOT NULL, INDEX IDX_667AD151E1605BE2 (incidencia_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE aviso (id INT AUTO_INCREMENT NOT NULL, titulo VARCHAR(150) NOT NULL, texto LONGTEXT NOT NULL, fecha DATETIME NOT NULL, comunidad_id INT NOT NULL, INDEX IDX_B23DF0F3B824C74B (comunidad_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE comunidad (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(150) NOT NULL, direccion VARCHAR(255) NOT NULL, pin VARCHAR(6) NOT NULL, administrador_id INT NOT NULL, INDEX IDX_2269477148DFEBB7 (administrador_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE incidencia (id INT AUTO_INCREMENT NOT NULL, codigo VARCHAR(10) NOT NULL, descripcion LONGTEXT NOT NULL, foto VARCHAR(255) DEFAULT NULL, piso VARCHAR(20) DEFAULT NULL, telefono VARCHAR(15) DEFAULT NULL, estado VARCHAR(255) NOT NULL, prioridad VARCHAR(255) DEFAULT NULL, fecha_creacion DATETIME NOT NULL, fecha_resolucion DATETIME DEFAULT NULL, cuantidad_reabierta INT NOT NULL, zona_id INT NOT NULL, UNIQUE INDEX UNIQ_C7C6728C20332D99 (codigo), INDEX IDX_C7C6728C104EA8FC (zona_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE zona (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(100) NOT NULL, tiene_qr TINYINT NOT NULL, comunidad_id INT NOT NULL, INDEX IDX_A786041EB824C74B (comunidad_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id))');
        $this->addSql('ALTER TABLE afectado ADD CONSTRAINT FK_667AD151E1605BE2 FOREIGN KEY (incidencia_id) REFERENCES incidencia (id)');
        $this->addSql('ALTER TABLE aviso ADD CONSTRAINT FK_B23DF0F3B824C74B FOREIGN KEY (comunidad_id) REFERENCES comunidad (id)');
        $this->addSql('ALTER TABLE comunidad ADD CONSTRAINT FK_2269477148DFEBB7 FOREIGN KEY (administrador_id) REFERENCES administrador (id)');
        $this->addSql('ALTER TABLE incidencia ADD CONSTRAINT FK_C7C6728C104EA8FC FOREIGN KEY (zona_id) REFERENCES zona (id)');
        $this->addSql('ALTER TABLE zona ADD CONSTRAINT FK_A786041EB824C74B FOREIGN KEY (comunidad_id) REFERENCES comunidad (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE afectado DROP FOREIGN KEY FK_667AD151E1605BE2');
        $this->addSql('ALTER TABLE aviso DROP FOREIGN KEY FK_B23DF0F3B824C74B');
        $this->addSql('ALTER TABLE comunidad DROP FOREIGN KEY FK_2269477148DFEBB7');
        $this->addSql('ALTER TABLE incidencia DROP FOREIGN KEY FK_C7C6728C104EA8FC');
        $this->addSql('ALTER TABLE zona DROP FOREIGN KEY FK_A786041EB824C74B');
        $this->addSql('DROP TABLE administrador');
        $this->addSql('DROP TABLE afectado');
        $this->addSql('DROP TABLE aviso');
        $this->addSql('DROP TABLE comunidad');
        $this->addSql('DROP TABLE incidencia');
        $this->addSql('DROP TABLE zona');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
