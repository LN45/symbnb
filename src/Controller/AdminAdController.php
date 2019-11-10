<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\CommandeType;
use App\Service\Pagination;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_index", requirements={"page":"\d+"})
     */
    public function index($page, Pagination $pagination)
    {
        // autre solution pour la route, mettre le requirements directement (on peut alors virer =1) @Route("/admin/ads/{page<\d+?1>}", name="admin_ads_index")

//        $ad = $repo->find(242);
//        $ad = $repo->findOneBy([
//            'id' => 242
//        ]);
//        $ads = $repo->findBy([], [], 5,0);
//        dump($ads);

//        $limit = 10;
//        $start = $page * $limit - $limit;
//        $total = count($repo->findAll());
//        $pages = ceil($total / $limit); //3.4 => 4

        $pagination->setEntityClass(Ad::class)
            ->setPage($page);

        return $this->render('admin/ad/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'édition
     * @param Ad $ad
     * @Route("/admin/ads/{id}/edit", name="admin_ads_edit")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Ad $ad, Request $request, ObjectManager $manager) {
        $form = $this->createForm(CommandeType::class, $ad);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée !"
            );
        }

        return $this->render('admin/ad/edit.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer une annonce
     *
     * @Route("/admin/ads/{id}/delete", name="admin_ads_delete")
     * @param Ad $ad
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Ad $ad, ObjectManager $manager) : Response
    {
        if (count($ad->getBookings()) > 0) {
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas supprimer l'annonce <strong>{$ad->getTitle()}</strong> car elle possède déjà des réservations !"
            );
        } else {
            $manager->remove($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée !"
            );
        }


        return $this->redirectToRoute('admin_ads_index');
    }
}
