<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Panier;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Form\PanierType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
    * @Route("/{_locale}")
    */
class FicheController extends AbstractController
{
    /**
     *@Route("/fiche", name="fiche")
     */
    public function index(Request $request, TranslatorInterface $translator)
    {
        $pdo = $this->getDoctrine()->getManager();
 
        $fiches = $pdo->getRepository(Panier::class)->findAll();

         $fiche = new Panier();
         $form = $this->createForm(PanierType::class, $fiche);
    
         $form->handleRequest($request);
         if($form->isSubmitted() && $form->isValid()){
             $pdo->persist($fiche); 
             $pdo->flush(); 

             $this->addFlash(
                 "success",
                 $translator->trans('fiche.added')
             );
         }
         
        return $this->render('fiche/index.html.twig', [
            'fiches' => $fiches,
            'form_fiche_new' => $form->createView()
        ]);
    }
    



        /**
     * @Route("/fiche{id}", name="fiche_edit")
     */

    public function fiche( Panier $fiche=null, Request $request){

        $form = $this->createForm(PanierType::class, $fiche);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $pdo = $this->getDoctrine()->getManager();
            $pdo->persist($fiche); 
            $pdo->flush(); 
        }
        return $this->render('fiche/fiche.html.twig', [
            'fiche' => $fiche,
            'form_fiche' => $form->createView()
        ]);
    }
    /**
     *@Route("/fiche/delete/{id}", name="delete_fiche")  
     */ 
    public function delete(Panier $fiche=null){
        if($fiche !=null){
            //On a trouvé un fiche, on le supprime 
            $pdo=$this->getDoctrine()->getManager();
            $pdo->remove($fiche);
            $pdo->flush();
        }

        return $this->redirectToRoute('fiche');
    }
}

