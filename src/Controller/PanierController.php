<?php

namespace App\Controller;

use App\Entity\Panier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Produit;
use App\Form\PanierType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Form\ProduitType;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
    * @Route("/{_locale}")
    */
class PanierController extends AbstractController
{
    /**
     *@Route("/panier", name="panier")  
     */ 
    public function index(Request $request, TranslatorInterface $translator)
    {
        // Récupère Doctrine (service de gestion de BDD)
        $pdo = $this->getDoctrine()->getManager();

        // Récupère tous les paniers
        $paniers = $pdo->getRepository(Panier::class)->findAll();
        $produits = $pdo->getRepository(Produit::class)->findAll();
        /**
         * ->findOneBy(['id' => 2])
         * ->findBy(['nom' => 'Nom du panier'])
         */
         $panier = new Panier();
         $form = $this->createForm(PanierType::class, $panier);
         
         // Analyse la requete HTTP
         $form->handleRequest($request);
         if($form->isSubmitted() && $form->isValid()){

             // Le formulaire a été envoyé, on le sauvegarde
             $pdo->persist($panier); // prepare
             $pdo->flush(); // execute


             $this->addFlash(
                "success",
                $translator->trans('categorie.added')
            );
             
         }
         

    

        return $this->render('panier/index.html.twig', [
            'paniers' => $paniers,
            'form_panier_new' => $form->createView(),
            'produits' => $produits
        ]);
    }

           /**
     * @Route("/panier/{id}", name="panier_edit")
     */

    public function note(Panier $panier=null, Request $request){

        $form = $this->createForm(PanierType::class, $panier);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $pdo = $this->getDoctrine()->getManager();
            $pdo->persist($panier); 
            $pdo->flush(); 
        }
        return $this->render('panier/panier.html.twig', [
            'panier' => $panier,
            'form' => $form->createView()
        ]);


    }
    /**
     *@Route("/panier/delete/{id}", name="delete_panier")  
     */ 
    public function delete(Panier $panier=null){
        if($panier !=null){
            //On a trouvé un panier, on le supprime 
            $pdo=$this->getDoctrine()->getManager();
            $pdo->remove($panier);
            $pdo->flush();
        }

        return $this->redirectToRoute('panier');
    }
}



