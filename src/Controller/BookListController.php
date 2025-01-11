<?php

namespace App\Controller;

use App\Repository\BookReadRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class BookListController
{
    #[Route('/books/list', name: 'app_books_list', methods: ['GET'])]
    public function list(BookReadRepository $bookReadRepository): JsonResponse
    {
        // Récupérer les livres en cours et terminés pour l'utilisateur (ID fixe pour l'instant)
        $userId = 1;
        
        $inProgressBooks = $bookReadRepository->findInProgressByUserId($userId);
        $finishedBooks = $bookReadRepository->findFinishedByUserId($userId);

        // Transformer les données pour la réponse JSON
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

        return new JsonResponse([
            'inProgress' => $inProgressData,
            'finished' => $finishedData
        ]);
    }
}
