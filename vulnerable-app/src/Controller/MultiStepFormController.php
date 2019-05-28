<?php declare(strict_types=1);

namespace App\Controller;

use App\Data\MultiStepForm;
use App\Session\SessionManager;
use App\Validator\MultiStepFormValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class MultiStepFormController extends AbstractController
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * @var MultiStepFormValidator
     */
    private $validator;

    public function __construct(SessionInterface $session, SessionManager $sessionManager, MultiStepFormValidator $validator)
    {
        $this->session = $session;
        $this->sessionManager = $sessionManager;
        $this->validator = $validator;
    }

    /**
     * @Route("/multi-step/step-1", methods={"GET", "POST"})
     */
    public function step1(Request $request): Response
    {
        if (!$request->isMethod('POST')) return $this->render('step1.html.twig');

        $vipDiscount = (bool)$request->request->get('vipDiscount');
        $address = $request->request->get('address');
        $form = new MultiStepForm($vipDiscount, $address);

        $errors = $this->validator->validate($this->sessionManager->getUser(), $form);
        if (count($errors)) {
            foreach ($errors as $error) {
                $this->session->getFlashBag()->add('danger', $error);
            }

            return $this->render('step1.html.twig');
        } else {
            $response = $this->redirect($this->generateUrl('app_multistepform_step2'));
            $response->headers->setCookie(new Cookie('multi-step', serialize($form), 0, '/', null, false, false));

            return $response;
        }
    }

    /**
     * @Route("/multi-step/step-2", methods={"GET", "POST"})
     */
    public function step2(Request $request): Response
    {
        if ($request->isMethod('POST')) return $this->redirect($this->generateUrl('app_multistepform_step3'));

        $multiStep = $this->deserializeMultiStepForm($request);

        if ($multiStep === null) return $this->redirect($this->generateUrl('app_multistepform_step1'));

        return $this->render('step2.html.twig', [
            'form' => $multiStep,
        ]);
    }

    /**
     * @Route("/multi-step/step-3", methods={"GET"})
     */
    public function step3(Request $request): Response
    {
        $multiStep = $this->deserializeMultiStepForm($request);
        if ($multiStep === null) return $this->redirect($this->generateUrl('app_multistepform_step1'));

        $response = $this->render('step3.html.twig', [
            'form' => $multiStep,
        ]);
        $response->headers->clearCookie('multi-step');

        return $response;
    }

    private function deserializeMultiStepForm(Request $request): ?MultiStepForm
    {
        $multiStep = $request->cookies->get('multi-step');

        return $multiStep ? unserialize($multiStep, [MultiStepForm::class]) : null;
    }
}
