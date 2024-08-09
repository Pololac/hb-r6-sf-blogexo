<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\ArticleRepository;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class AuthorController extends AbstractController
{
    #[Route('/authors', name: 'authors_list')]
    public function list(AuthorRepository $authorRepository): Response
    {
        $authors = $authorRepository->findAll();

        return $this->render('author/list.html.twig', [
            'authors' => $authors,
        ]);
    }

    #[Route('/author/{id}', name: 'author_item')]
    public function item(AuthorRepository $authorRepository, ArticleRepository $articleRepository, int $id=0): Response 
    {
        $author = $authorRepository->find($id);
        $articles = $author->getArticles();

        if ($author === null) {                // "Throw early pattern"
            throw new NotFoundHttpException('Article introuvable');
        }

        return $this->render('author/item.html.twig', [
                'author' => $author,
                'articles' => $articles,
        ]);
    
    }




}
