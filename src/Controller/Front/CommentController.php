<?php


namespace App\Controller\Front;


use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CommentController
 * @package App\Controller
 * @Route("/comment", name="comment.")
 */
class CommentController extends AbstractController
{

    /**
     * @IsGranted(App\Security\CommentVoter::DELETE, subject="comment")
     * @Route("/comment/{id}/remove", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param Comment $comment
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function delete(Request $request, Comment $comment, TranslatorInterface $translator): Response
    {
        $rojectId = $comment->getProject()->getId();
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
            $this->addFlash('success', $translator->trans('app.flash_message.comment_delete_success'));
        }
        return $this->redirectToRoute('app.project.show', [
            'id' => $rojectId,
            'context' => 'call_of_project'
        ]);
    }

}
