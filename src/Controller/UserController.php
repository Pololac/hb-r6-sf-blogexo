<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


class UserController extends AbstractController
{
    #[Route('/user/edit/{id}', name: 'user_edit')]
    public function edit(
        Request $request,
        User $user,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        ): Response
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $profilePic */
            $profilePic = $form->get('profilePic')->getData();
        
            if ($profilePic) {
                // On récupère le nom original du fichier, tel que nommé par le client
                // La méthode getClientOriginalName est disponible sur les instances de UploadedFile
                $originalFilename = pathinfo($profilePic->getClientOriginalName(), PATHINFO_FILENAME);
                // On en génère une nouvelle version sans caractères spéciaux, sans accents, etc... On appelle ça un "slug"
                // Exercice : trouvez comment avoir cette variable $slugger
                // Indice   : explorez dans les types autowirés ce que le container vous propose pour le terme "slugger" (php bin/console debug:autowiring slugger). Dès que vous avez trouvé le type correspondant, effectuez un type-hint et nommez le paramètre $slugger
                $safeFilename = $slugger->slug($originalFilename);
                // On peut alors construire le nom du fichier tel qu'il sera stocké sur le serveur
                $filename = $safeFilename . '-' . uniqid() . '.' . $profilePic->guessExtension();
        
                try {
                    // À la manière de move_uploaded_file en PHP, on tente ici de déplacer le fichier de sa zone de transit à sa destination. Cette méthode peut lancer des exceptions : on encadre donc l'appel par un bloc try...catch
                    $profilePic->move(
                        'uploads/user/',
                        $filename
                    );
                    // Ici, nettoyage avant de modifier le nom du fichier
                    if ($user->getProfilePicFilename() !== null) {
                        unlink(__DIR__ . "/../../public/uploads/user/" . $user->getProfilePicFilename());
                    }
                    // Si on n'est pas passé dans le catch, alors on peut enregistrer le nom du fichier dans la propriété profilePicFilename de l'utilisateur
                    $user->setProfilePicFilename($filename);

                } catch (FileException $e) {
                    $form->addError(new FormError("Erreur lors de l'upload du fichier"));
                }
            }
        
            // De toute façon, on met à jour l'enregistrement en base de données
            $em->flush();
        }
    
        return $this->render('user/edit.html.twig', [
            'form' => $form,
            'user' => $user
        ]);
    }
}