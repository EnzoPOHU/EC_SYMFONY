<?php

namespace App\Controller;

use App\Entity\BookRead;
use App\Repository\BookReadRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur pour la gestion des lectures de livres
 * 
 * Ce contrôleur gère les opérations liées à l'ajout et au suivi 
 * des livres lus par un utilisateur.
 */
class BookReadController extends AbstractController
{
    /**
     * Ajoute un nouveau livre lu à la liste de l'utilisateur
     * 
     * Cette méthode permet d'enregistrer un livre comme lu ou en cours de lecture,
     * avec des détails supplémentaires comme une description et une note.
     * 
     * @param Request $request Requête HTTP contenant les données du livre
     * @param BookRepository $bookRepository Référentiel pour récupérer les livres
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités Doctrine
     * 
     * @return Response Réponse HTTP (redirection ou erreur)
     */
    #[Route('/book-read/add', name: 'book_read.add', methods: ['POST'])]
    public function add(
        Request $request,
        BookRepository $bookRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Récupération des données du formulaire
        $bookId = $request->request->get('book');
        $description = $request->request->get('description');
        $rating = $request->request->get('rating');
        $isFinished = $request->request->getBoolean('is_finished');

        // Validation de base : vérification de l'existence du livre
        if (!$bookId) {
            return new JsonResponse(['error' => 'Veuillez sélectionner un livre'], Response::HTTP_BAD_REQUEST);
        }

        $book = $bookRepository->find($bookId);
        if (!$book) {
            return new JsonResponse(['error' => 'Le livre sélectionné n\'existe pas'], Response::HTTP_BAD_REQUEST);
        }

        // Création de la nouvelle lecture
        $bookRead = new BookRead();
        $bookRead->setBook($book);
        $bookRead->setUserId(1); // Pour le moment, on utilise un ID fixe
        $bookRead->setDescription($description);
        $bookRead->setRating((string) $rating);
        $bookRead->setIsRead($isFinished);
        $bookRead->setCreatedAt(new \DateTime());
        $bookRead->setUpdatedAt(new \DateTime());

        // Persistance en base de données
        $entityManager->persist($bookRead);
        $entityManager->flush();

        // Redirection vers la page d'accueil
        return $this->redirectToRoute('app.home');
    }
}