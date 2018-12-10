<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MaliciousController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function phishing(): Response
    {
        return $this->render('phishing.html.twig');
    }

    /**
     * @Route("/cross-site-request-forgery")
     */
    public function csrf(): Response
    {
        return $this->render('csrf.html.twig');
    }
}
