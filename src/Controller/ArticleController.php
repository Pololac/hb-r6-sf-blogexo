<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArticleController extends AbstractController
{
    #[Route('/articles', name: 'articles_list')]
    public function list(ArticleRepository $articleRepository): Response 
    {
        $articles = $articleRepository->findAll();
        
        return $this->render('article/list.html.twig', [
            'articles' => $articles,
            ]
        );

    }

    #[Route('/article/{id}', name: 'article_item')]
    public function item(Article $article): Response 
    {
        
        return $this->render('article/item.html.twig', [
            'article' => $article,
            ]
        );

    }


}
