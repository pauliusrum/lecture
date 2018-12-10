<?php declare(strict_types=1);

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Session\SessionManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class CommentsController extends AbstractController
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * @var CommentRepository
     */
    private $commentRepository;

    public function __construct(SessionInterface $session, CommentRepository $commentRepository, AuthenticationUtils $authenticationUtils, SessionManager $sessionManager)
    {
        $this->session = $session;
        $this->commentRepository = $commentRepository;
        $this->authenticationUtils = $authenticationUtils;
        $this->sessionManager = $sessionManager;
    }

    /**
     * @Route("/", methods={"GET", "POST"})
     */
    public function index(): Response
    {
        return $this->render('index.html.twig', [
            'comments' => $this->commentRepository->findAllOrdered(),
            'last_username' => $this->authenticationUtils->getLastUsername(),
            'login_error' => $this->authenticationUtils->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/comment/{id<\d+>}", methods={"GET", "POST"})
     */
    public function editComment(Int $id, Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $this->commentRepository->update($id, $request->request->get('contents'));
            $this->session->getFlashBag()->add('success', 'Updated.');
        }

        return $this->render('details.html.twig', [
            'comment' => $this->commentRepository->find($id),
        ]);
    }

    /**
     * @Route("/remove-comment", methods={"POST"})
     */
    public function removeComment(Request $request): Response
    {
        try {
            $this->commentRepository->remove($request->request->getInt('id'));
            $this->session->getFlashBag()->add('success', 'Removed.');
        } catch (Throwable $e) {
            $this->session->getFlashBag()->add('danger', $e->getMessage());
        }

        return $this->redirect($this->generateUrl('app_comments_index'));
    }

    /**
     * @Route("/save-comment", methods={"POST"})
     */
    public function saveComment(Request $request): Response
    {
        try {
            $user = $this->sessionManager->getUser();
            $userId = $user ? $user->getId() : null;
            $contents = $request->request->get('contents');

            $this->commentRepository->save($userId, $contents);
            $this->session->getFlashBag()->add('success', 'Added.');
        } catch (Throwable $e) {
            $this->session->getFlashBag()->add('danger', $e->getMessage());
        }

        return $this->redirect($this->generateUrl('app_comments_index'));
    }
}
