<?php

namespace App\Controller;

use App\Repository\BookReadRepository;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    private BookReadRepository $readBookRepository;
    private BookRepository $bookRepository;

    public function __construct(BookReadRepository $bookReadRepository, BookRepository $bookRepository)
    {
        $this->readBookRepository = $bookReadRepository;
        $this->bookRepository = $bookRepository;
    }

    #[Route('/', name: 'app.home')]
    public function index(): Response
    {
        $userId = 1;
        $finishedBooks = $this->readBookRepository->findFinishedByUserId($userId);
        $inProgressBooks = $this->readBookRepository->findInProgressByUserId($userId);
        $allBooks = $this->bookRepository->findAll();

        return $this->render('pages/home.html.twig', [
            'booksRead' => $finishedBooks,
            'booksInProgress' => $inProgressBooks,
            'allBooks' => $allBooks,
            'name' => 'Accueil',
        ]);
    }
}
