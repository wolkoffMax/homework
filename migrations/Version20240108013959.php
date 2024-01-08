<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240108013959 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE accounts (id UUID NOT NULL, client_id UUID DEFAULT NULL, balance NUMERIC(10, 2) NOT NULL, currency VARCHAR(3) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_accounts_client_id ON accounts (client_id)');
        $this->addSql('COMMENT ON COLUMN accounts.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN accounts.client_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN accounts.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN accounts.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE clients (id UUID NOT NULL, full_name VARCHAR(100) NOT NULL, username VARCHAR(50) NOT NULL, password_hash VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN clients.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN clients.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN clients.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE currency_rates (id UUID NOT NULL, base_currency VARCHAR(3) NOT NULL, target_currency VARCHAR(3) NOT NULL, rate DOUBLE PRECISION NOT NULL, conversion_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_currency_rate_bse_currency_target_currency ON currency_rates (base_currency, target_currency)');
        $this->addSql('COMMENT ON COLUMN currency_rates.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN currency_rates.conversion_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN currency_rates.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN currency_rates.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE transactions (id UUID NOT NULL, account_id UUID DEFAULT NULL, type VARCHAR(15) NOT NULL, amount NUMERIC(10, 2) NOT NULL, currency VARCHAR(3) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_transactions_account_id ON transactions (account_id)');
        $this->addSql('COMMENT ON COLUMN transactions.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN transactions.account_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN transactions.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN transactions.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE accounts ADD CONSTRAINT FK_CAC89EAC19EB6921 FOREIGN KEY (client_id) REFERENCES clients (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transactions ADD CONSTRAINT FK_EAA81A4C9B6B5FBA FOREIGN KEY (account_id) REFERENCES accounts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE accounts DROP CONSTRAINT FK_CAC89EAC19EB6921');
        $this->addSql('ALTER TABLE transactions DROP CONSTRAINT FK_EAA81A4C9B6B5FBA');
        $this->addSql('DROP TABLE accounts');
        $this->addSql('DROP TABLE clients');
        $this->addSql('DROP TABLE currency_rates');
        $this->addSql('DROP TABLE transactions');
    }
}
