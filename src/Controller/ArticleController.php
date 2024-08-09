<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\AddArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/articles/add', name: 'articles_add', methods: ['GET', 'POST'])]
    public function addArticle(
        Request $request,
        EntityManagerInterface $em
        ): Response
    {
        $article = new Article();
        $form = $this->createForm(AddArticleType::class, $article);     //Crée le formulaire en utilisant le modèle défini dans AddArticleType
        
        // Prends en charge la requête entrante et s'il y a des données POST, les met dans $newsletter
        $form->handleRequest($request);

        // Enregistrement de mon email
        if ($form->isSubmitted() && $form->isValid()) {
            // dd($newsletter);
            $em->persist($article);
            $em->flush();
            $this->addFlash('success', 'Votre article a bien été ajouté. Merci!');

            return $this->redirectToRoute('add_confirm');
        }    

        //Affiche formulaire si rien dans POST
        return $this->render('article/add.html.twig', [
            'addArticle' => $form
        ]);
    }

    #[Route('/articles/thanks', name: "add_confirm")]
    public function newsletterConfirm() : Response{
        return $this->render('article/add_confirm.html.twig');
    }


}
