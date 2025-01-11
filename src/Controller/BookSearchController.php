<?php

namespace App\Controller;

use App\Repository\BookReadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur de recherche de livres
 * 
 * Ce contrôleur gère la recherche de livres pour un utilisateur,
 * en filtrant à la fois les livres en cours et terminés.
 */
class BookSearchController extends AbstractController
{
    /**
     * Référentiel pour les opérations de recherche de livres lus
     * 
     * @var BookReadRepository
     */
    private $bookReadRepository;

    /**
     * Constructeur du contrôleur
     * 
     * Initialise le référentiel de recherche de livres
     * 
     * @param BookReadRepository $bookReadRepository Dépôt pour les opérations de recherche
     */
    public function __construct(BookReadRepository $bookReadRepository)
    {
        $this->bookReadRepository = $bookReadRepository;
    }

    /**
     * Recherche de livres par terme
     * 
     * Cette méthode permet de rechercher des livres en cours et terminés
     * pour un utilisateur donné, en fonction d'un terme de recherche.
     * 
     * @Route("/books/search", name="app_books_search", methods={"GET"})
     * 
     * @param Request $request Requête HTTP contenant le terme de recherche
     * @return JsonResponse Liste des livres correspondant à la recherche
     */
    public function searchBooks(Request $request): JsonResponse
    {
        // Récupération du terme de recherche
        $searchTerm = $request->query->get('term', '');
        $userId = 1; // À remplacer par l'utilisateur connecté

        // Recherche dans les livres en cours
        $inProgressBooks = $this->bookReadRepository->searchInProgressBooksByUser($userId, $searchTerm);
        
        // Recherche dans les livres terminés
        $finishedBooks = $this->bookReadRepository->searchFinishedBooksByUser($userId, $searchTerm);

        // Transformation des livres en cours en données JSON
        $inProgressData = array_map(function($bookRead) {
            return [
                'id' => $bookRead->getId(),
                'book' => [
                    'name' => $bookRead->getBook()->getName(),
                    'categoryName' => $bookRead->getBook()->getCategory()->getName()
                ],
                'description' => $bookRead->getDescription(),
                'rating' => $bookRead->getRating()
            ];
        }, $inProgressBooks);

        // Transformation des livres terminés en données JSON
        $finishedData = array_map(function($bookRead) {
            return [
                'id' => $bookRead->getId(),
                'book' => [
                    'name' => $bookRead->getBook()->getName(),
                    'categoryName' => $bookRead->getBook()->getCategory()->getName()
                ],
                'description' => $bookRead->getDescription(),
                'rating' => $bookRead->getRating()
            ];
        }, $finishedBooks);

        // Retour des résultats de recherche
        return $this->json([
            'inProgress' => $inProgressData,
            'finished' => $finishedData
        ]);
    }
}
