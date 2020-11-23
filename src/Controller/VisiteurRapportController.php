<?php

namespace App\Controller;
use App\Service\Pagination;
use App\Service\PaginationService;
use App\Form\RapportType;
use App\Entity\RapportVisite;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RapportVisiteRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VisiteurRapportController extends AbstractController
{
    /**
     * @Route("/rapports/{page<\d+>?1}", name="visiteur_rapports_index")
     */
    public function index(RapportVisiteRepository $repo, $page, Pagination $pagination )
    {
       /* $limit = 10;
        $start = $page * $limit - $limit;

        //Calcul des pages
        $total = count($repo->findAll());
        $pages = ceil($total / $limit);*/
        $pagination->setEntityClass(RapportVisite::class)
                ->setPage($page);
               

<<<<<<< HEAD
        return $this->render('rapport/index.html.twig', [
           'rapports' => $repo->findBy([], [], $limit, $start),
           'pages' => $pages,
           'page' => $page
=======
        return $this->render('admin/rapport/index.html.twig', [
          'pagination' =>$pagination
>>>>>>> 63c4cfeea2f1bae4a65a619e48fc1ec3988a652f
        ]);
    }
   
   
   /**
    * Permet d'afficher le formulaire d'edition
    *
    *@Route("/rapports/{id}/edit" , name="admin_rapports_edit")
    * @param RapportVisite $rapport
    * @return Response
    */
    public function edit(RapportVisite $rapport, Request $request, EntityManagerInterface $manager ){
        $form = $this->createForm(RapportType::class, $rapport);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($rapport);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le rapport <strong>{$rapport->getId()}<strong> à bien été enregistrer !"
            );

        }
        return $this->render('admin/rapport/edit.html.twig' , [
            'rapport' => $rapport,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer un rapport de visite ! 
     *@Route("/admin/rapports/{id}/delete", name="admin_rapports_delete")
     * @param RapportVisite $rapport
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(RapportVisite $rapport, EntityManagerInterface $manager){
        if (count($rapport->getOffrirs()) > 0) {
           $this->addFlash(
               'warning',
               "Vous ne pouvez pas supprimer le rapport <strong>{$rapport->getId()} </strong> "

           );
        }

        else{
            $manager->remove($rapport);
            $manager->flush();
    
            $this->addFlash(
                'success',
                "Le rapport <strong>{$rapport->getId()}</strong> a bien été supprimée ! "
            );
        }

        return $this->redirectToRoute('visiteur_rapports_index');
    }


}