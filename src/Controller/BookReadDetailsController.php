<?php

namespace App\Controller;

use App\Repository\BookReadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur de gestion des détails de lecture de livres
 * 
 * Ce contrôleur permet de récupérer les informations détaillées 
 * d'un livre lu par un utilisateur.
 */
class BookReadDetailsController extends AbstractController
{
    /**
     * Référentiel pour les opérations sur les livres lus
     * 
     * @var BookReadRepository
     */
    private $bookReadRepository;

    /**
     * Constructeur du contrôleur
     * 
     * Initialise le référentiel pour les opérations sur les livres lus
     * 
     * @param BookReadRepository $bookReadRepository Dépôt pour les opérations sur les livres lus
     */
    public function __construct(BookReadRepository $bookReadRepository)
    {
        $this->bookReadRepository = $bookReadRepository;
    }

    /**
     * Récupère les détails d'un livre lu
     * 
     * Cette méthode retourne les informations complètes d'un livre 
     * lu par l'utilisateur connecté.
     * 
     * @Route("/book-read/{id}/details", name="app_book_read_details", methods={"GET"})
     * 
     * @param int $id Identifiant du livre lu
     * @return JsonResponse Détails du livre au format JSON
     */
    public function getBookReadDetails(int $id): JsonResponse
    {
        // Rechercher le livre lu par son identifiant
        $bookRead = $this->bookReadRepository->find($id);

        // Vérifier si le livre existe
        if (!$bookRead) {
            return $this->json(['error' => 'Livre non trouvé'], 404);
        }

        // Retourner les détails du livre
        return $this->json([
            'id' => $bookRead->getId(),
            'book' => [
                'id' => $bookRead->getBook()->getId(),
                'name' => $bookRead->getBook()->getName(),
                'category' => $bookRead->getBook()->getCategory()->getName()
            ],
            'description' => $bookRead->getDescription(),
            'rating' => $bookRead->getRating(),
            'isRead' => $bookRead->isRead()
        ]);
    }
}
