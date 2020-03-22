<?php

namespace App\Controller;

use App\Entity\Panier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Produit;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Form\ProduitType;
use App\Form\PanierType;
use App\Repository\ProduitRepository;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
    * @Route("/{_locale}")
    */
class ProduitController extends AbstractController
{
    /**
     *@Route("/produit", name="produit")
     */
    public function index(Request $request, TranslatorInterface $translator)
    {
        $pdo = $this->getDoctrine()->getManager();
 
        $produits = $pdo->getRepository(Produit::class)->findAll();
        $paniers = $pdo->getRepository(Panier::class)->findAll();
        

         $produit = new Produit();
         $form = $this->createForm(ProduitType::class, $produit);
    
         $form->handleRequest($request);
         if($form->isSubmitted() && $form->isValid()){

            $fichier = $form->get('photo')->getData();
            if($fichier){
                $nomFichier = uniqid() .'.'. $fichier->guessExtension();
                try{
                    $fichier->move(
                        $this->getParameter('upload_dir'),
                        $nomFichier
                    );
                }
                catch(FileException $e){
                    $this->addFlash('danger', "Impossible d'uploader le fichier");
                    return $this->redirectToRoute('home');
                }
                $produit->setPhoto($nomFichier);
            }
            


             $pdo->persist($produit); 
             $pdo->flush(); 

             $this->addFlash(
                 "success",
                 $translator->trans('produit.added')
             );
         }
         
        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
            'form_produit_new' => $form->createView(),
            'paniers' => $paniers

        ]);
    }
    



        /**
     * @Route("/produit{id}", name="produit_edit")
     */

    public function produit( Produit $produit=null, Request $request){

        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $pdo = $this->getDoctrine()->getManager();
            $pdo->persist($produit); 
            $pdo->flush(); 
        }
        return $this->render('produit/produit.html.twig', [
            'produit' => $produit,
            'form_panier_new' => $form->createView()
        ]);
    }


    /**
     *@Route("/produit/delete/{id}", name="delete_produit")  
     */ 
    public function delete(Produit $produit=null){
        if($produit !=null){
            //On a trouvé un produit, on le supprime 
            $pdo=$this->getDoctrine()->getManager();
            $pdo->remove($produit);
            $pdo->flush();
        }

        return $this->redirectToRoute('produit');
    }
}

