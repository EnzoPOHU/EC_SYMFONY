<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:purge-database',
    description: 'Completely purge the database, bypassing foreign key constraints'
)]
class PurgeDatabaseCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $connection = $this->entityManager->getConnection();

        // Désactiver les contraintes de clé étrangère
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');

        // Liste des tables à purger (dans l'ordre pour éviter les erreurs de contrainte)
        $tables = [
            'book_read',
            'book',
            'category'
        ];

        // Purger chaque table
        foreach ($tables as $table) {
            $connection->executeQuery("TRUNCATE TABLE $table");
            $io->writeln("Purged table: $table");
        }

        // Réactiver les contraintes de clé étrangère
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');

        $io->success('Database has been completely purged');

        return Command::SUCCESS;
    }
}
