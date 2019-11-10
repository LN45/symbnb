<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Service\Pagination;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommentController extends AbstractController
{
    /**
     * Liste tous les commentaires
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comments")
     */
    public function index($page, Pagination $pagination)
    {
        $pagination->setEntityClass(Comment::class)
            ->setLimit(5)
            ->setPage($page);

        return $this->render('admin/comment/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet de modifier un commentaire
     * @Route("/admin/comments/{id}/edit", name="admin_comment_edit")
     * @param Comment $comment
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Comment $comment, ObjectManager $manager, Request $request)
    {
        $form = $this->createForm(AdminCommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les modifications sur le commentaire n° {$comment->getId()} de <strong>{$comment->getAuthor()->getFullName()}</strong> ont bien été prises en compte !"
            );
        }

        return $this->render('admin/comment/edit.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment
        ]);
    }

    /**
     * Permet de supprimer un commentaire
     * @Route("/admin/comments/{id}/delete", name="admin_comment_delete")
     * @param ObjectManager $manager
     * @param Comment $comment
     * @return Response
     */
    public function delete(Comment $comment, ObjectManager $manager) : Response
    {
        $manager->remove($comment);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le commentaire de <strong>{$comment->getAuthor()->getFullName()}</strong> a bien été supprimé !"
        );

        return $this->redirectToRoute('admin_comments');
    }
}
