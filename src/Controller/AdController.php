<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\CommandeType;
use App\Repository\AdRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     * @param AdRepository $adRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(AdRepository $adRepository)
    {
        $ads = $adRepository->findAll();
        return $this->render('ad/index.html.twig', [
            "ads" => $ads
        ]);
    }

    /**
     * Permet de créer une annonce
     * @Route("/ads/new", name="ads_create")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request, ObjectManager $manager)
    {
        $ad = new Ad();

        $form = $this->createForm(CommandeType::class, $ad);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            //on boucle sur chaque image pour les perister
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée !"
            );

            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }
        return $this->render('ad/new.html.twig', [
            'form' => $form->createView()
            ]);
    }

    /**
     * Permet d'afficher le formulaire d'édition
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Request $request, Ad $ad, ObjectManager $manager)
    {
        $form = $this->createForm(CommandeType::class, $ad);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            //on boucle sur chaque image pour les persister
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les modifications de l'annonce <strong>{$ad->getTitle()}</strong> ont bien été enregistrées !"
            );

            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }

        return $this->render("ad/edit.html.twig", [
            'form' => $form->createView(),
            'ad' => $ad
        ]);
    }




//    /**
//     * Affiche une seule annonce
//     * @Route("/ads/{slug}", name="ads_show")
//     * @return \Symfony\Component\HttpFoundation\Response
//     */
//    public function show($slug, AdRepository $adRepository)
//    {
//        $ad = $adRepository->findOneBySlug($slug); // récupère annonce qui correspond au slug
//
//        return $this->render('ad/show.html.twig', [
//        "ad" => $ad
//    ]);
//    }

    /**
     * Affiche une seule annonce grâce au paramconverter de Symfony
     * @Route("/ads/{slug}", name="ads_show")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Ad $ad)
    {
        return $this->render('ad/show.html.twig', [
            "ad" => $ad
        ]);
    }
}
