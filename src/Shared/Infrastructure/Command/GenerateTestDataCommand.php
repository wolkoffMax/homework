<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Command;

use App\Account\Domain\Account;
use App\Account\Domain\AccountRepository;
use App\Client\Domain\Client;
use App\Client\Domain\ClientFactory;
use App\Client\Domain\ClientRepository;
use App\Transaction\Domain\Transaction;
use App\Transaction\Domain\TransactionFactory;
use App\Transaction\Domain\TransactionRepository;
use App\Transaction\Domain\TransactionType;
use Faker\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class GenerateTestDataCommand extends Command
{
    protected static $defaultName = 'app:generate-test-data';

    private ClientRepository $clients;
    private AccountRepository $accounts;
    private TransactionRepository $transactions;

    public function __construct(ClientRepository $clients, AccountRepository $accounts, TransactionRepository $transactions)
    {
        parent::__construct();

        $this->clients = $clients;
        $this->accounts = $accounts;
        $this->transactions = $transactions;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generates a number of test clients.')
            ->addArgument('count', InputArgument::REQUIRED, 'The number of clients to generate');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $count = (int) $input->getArgument('count');

        for ($i = 0; $i < $count; ++$i) {
            $client = $this->generateClient();

            $output->writeln("Created client: {$client->fullName()}");

            $accountCount = random_int(0, 3);

            if ($accountCount < 1) {
                continue;
            }

            for ($j = 0; $j < $accountCount; ++$j) {
                $account = $this->generateAccount($client);

                $output->writeln("Created account: {$account->currency()} {$account->balance()}");

                $transactionCount = random_int(0, 5);

                if ($transactionCount < 1) {
                    continue;
                }

                for ($k = 0; $k < $transactionCount; ++$k) {
                    $transaction = $this->generateTransaction($account);

                    $output->writeln("Created transaction: {$transaction->type()->value} {$transaction->amount()}");
                }
            }
        }

        return Command::SUCCESS;
    }

    private function generateClient(): Client
    {
        $faker = Factory::create();

        $fullName = $faker->name;
        $username = $faker->userName;
        $password = $faker->sha256;

        $client = ClientFactory::create($fullName, $username, $password);

        $this->clients->add($client);

        return $client;
    }

    private function generateAccount(Client $client): Account
    {
        $currencies = ['USD', 'EUR', 'GBP'];

        $currency = $currencies[random_int(0, 2)];
        $amount = (string) random_int(100, 100000);

        $account = new Account($client, $currency, $amount);

        $this->accounts->add($account);

        return $account;
    }

    private function generateTransaction(Account $account): Transaction
    {
        $type = random_int(0, 1) ? TransactionType::INCOMING : TransactionType::OUTGOING;

        $amount = (string) random_int(100, 100000);

        $transaction = TransactionFactory::create(
            $type,
            $account,
            $amount,
            $account->currency()
        );

        $this->transactions->add($transaction);

        return $transaction;
    }
}
