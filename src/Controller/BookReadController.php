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

class BookReadController extends AbstractController
{
    #[Route('/book-read/add', name: 'book_read.add', methods: ['POST'])]
    public function add(
        Request $request,
        BookRepository $bookRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $bookId = $request->request->get('book');
        $description = $request->request->get('description');
        $rating = $request->request->get('rating');
        $isFinished = $request->request->getBoolean('is_finished');

        // Validation de base
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

        $entityManager->persist($bookRead);
        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Votre lecture a été ajoutée avec succès !',
            'bookRead' => [
                'id' => $bookRead->getId(),
                'book' => [
                    'id' => $book->getId(),
                    'name' => $book->getName(),
                    'categoryId' => $book->getCategoryId(),
                ],
                'description' => $bookRead->getDescription(),
                'rating' => $bookRead->getRating(),
                'isRead' => $bookRead->isRead(),
            ]
        ]);
    }
}
