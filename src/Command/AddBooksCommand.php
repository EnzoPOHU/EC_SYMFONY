<?php

namespace App\Command;

use App\Entity\Book;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:add-books',
    description: 'Add a large number of books to the database'
)]
class AddBooksCommand extends Command
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

        // Récupérer les catégories existantes
        $categoryRepository = $this->entityManager->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        // Si pas de catégories, créer des catégories par défaut
        if (empty($categories)) {
            $categoryNames = ['Roman', 'Science-fiction', 'Biographie', 'Philosophie', 'Histoire'];
            foreach ($categoryNames as $name) {
                $category = new Category();
                $category->setName($name);
                $category->setDescription("Catégorie $name");
                $category->setCreatedAt(new \DateTime());
                $category->setUpdatedAt(new \DateTime());
                $this->entityManager->persist($category);
            }
            $this->entityManager->flush();
            $categories = $categoryRepository->findAll();
        }

        // Liste de livres à ajouter
        $booksData = [
            ['name' => 'Le Monde de Sophie', 'description' => 'Un roman philosophique de Jostein Gaarder', 'category' => 'Philosophie', 'pages' => 512, 'publication_date' => '1991-01-01'],
            ['name' => 'L\'Alchimiste', 'description' => 'Un roman philosophique de Paulo Coelho', 'category' => 'Roman', 'pages' => 224, 'publication_date' => '1988-01-01'],
            ['name' => '1984', 'description' => 'Un roman dystopique de George Orwell', 'category' => 'Science-fiction', 'pages' => 328, 'publication_date' => '1949-06-08'],
            ['name' => 'Dune', 'description' => 'Un classique de la science-fiction de Frank Herbert', 'category' => 'Science-fiction', 'pages' => 412, 'publication_date' => '1965-08-01'],
            ['name' => 'Le Petit Prince', 'description' => 'Un conte philosophique de Saint-Exupéry', 'category' => 'Roman', 'pages' => 96, 'publication_date' => '1943-04-06'],
            ['name' => 'Sapiens', 'description' => 'Une brève histoire de l\'humanité par Yuval Noah Harari', 'category' => 'Histoire', 'pages' => 512, 'publication_date' => '2014-02-10'],
            ['name' => 'Les Misérables', 'description' => 'Un roman épique de Victor Hugo', 'category' => 'Roman', 'pages' => 1488, 'publication_date' => '1862-01-01'],
            ['name' => 'Fondation', 'description' => 'Le premier tome de la série de science-fiction d\'Isaac Asimov', 'category' => 'Science-fiction', 'pages' => 256, 'publication_date' => '1951-10-01'],
            ['name' => 'La Nuit des temps', 'description' => 'Un roman de René Barjavel', 'category' => 'Science-fiction', 'pages' => 320, 'publication_date' => '1968-01-01'],
            ['name' => 'Le Parfum', 'description' => 'Un roman de Patrick Süskind', 'category' => 'Roman', 'pages' => 272, 'publication_date' => '1985-01-01'],
            ['name' => 'Ainsi parlait Zarathoustra', 'description' => 'Un ouvrage philosophique de Friedrich Nietzsche', 'category' => 'Philosophie', 'pages' => 352, 'publication_date' => '1883-01-01'],
            ['name' => 'La Guerre des mondes', 'description' => 'Un roman de science-fiction de H.G. Wells', 'category' => 'Science-fiction', 'pages' => 192, 'publication_date' => '1898-01-01'],
            ['name' => 'Candide', 'description' => 'Un conte philosophique de Voltaire', 'category' => 'Philosophie', 'pages' => 160, 'publication_date' => '1759-01-01'],
            ['name' => 'Autant en emporte le vent', 'description' => 'Un roman historique de Margaret Mitchell', 'category' => 'Roman', 'pages' => 1024, 'publication_date' => '1936-06-30'],
            ['name' => 'Le Meilleur des mondes', 'description' => 'Un roman dystopique d\'Aldous Huxley', 'category' => 'Science-fiction', 'pages' => 272, 'publication_date' => '1932-10-27'],
            ['name' => 'Chroniques martiennes', 'description' => 'Un recueil de nouvelles de Ray Bradbury', 'category' => 'Science-fiction', 'pages' => 256, 'publication_date' => '1950-05-01'],
            ['name' => 'Madame Bovary', 'description' => 'Un roman de Gustave Flaubert', 'category' => 'Roman', 'pages' => 368, 'publication_date' => '1857-12-15'],
            ['name' => 'L\'Étranger', 'description' => 'Un roman d\'Albert Camus', 'category' => 'Roman', 'pages' => 159, 'publication_date' => '1942-06-01'],
            ['name' => 'De la Terre à la Lune', 'description' => 'Un roman de Jules Verne', 'category' => 'Science-fiction', 'pages' => 288, 'publication_date' => '1865-01-01'],
            ['name' => 'Germinal', 'description' => 'Un roman d\'Émile Zola', 'category' => 'Roman', 'pages' => 496, 'publication_date' => '1885-01-01']
        ];

        $booksData = array_merge($booksData);

        $addedCount = 0;
        foreach ($booksData as $bookData) {
            // Trouver la catégorie correspondante
            $category = $categoryRepository->findOneBy(['name' => $bookData['category']]);

            if (!$category) {
                // Créer la catégorie si elle n'existe pas
                $category = new Category();
                $category->setName($bookData['category']);
                $category->setDescription("Catégorie {$bookData['category']}");
                $category->setCreatedAt(new \DateTime());
                $category->setUpdatedAt(new \DateTime());
                $this->entityManager->persist($category);
            }

            $book = new Book();
            $book->setName($bookData['name']);
            $book->setDescription($bookData['description']);
            $book->setCategory($category);
            $book->setPages($bookData['pages']);
            $book->setPublicationDate(new \DateTime($bookData['publication_date']));
            $book->setCreatedAt(new \DateTime());
            $book->setUpdatedAt(new \DateTime());

            $this->entityManager->persist($book);
            $addedCount++;
        }

        $this->entityManager->flush();

        $io->success(sprintf('Added %d books to the database', $addedCount));

        return Command::SUCCESS;
    }
}
