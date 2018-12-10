<?php declare(strict_types=1);

namespace App\Controller;

use App\Data\MultiStepForm;
use App\Session\SessionManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MultiStepFormController extends AbstractController
{
    /**
     * @var SessionManager
     */
    private $sessionManager;

    public function __construct(SessionManager $sessionManager)
    {
        $this->sessionManager = $sessionManager;
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

        $this->validate($form);

        $response = $this->redirect($this->generateUrl('app_multistepform_step2'));
        $response->headers->setCookie(new Cookie('multi-step', serialize($form), 0, '/', null, false, false));

        return $response;
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

    private function validate(MultiStepForm $form): void
    {
        if ($form->isVipDiscount()) {
            $user = $this->sessionManager->getUser();
            $roles = $user ? $user->getRoles() : [];

            if ($user && !in_array('ROLE_VIP', $roles, true)) throw new RuntimeException('Only VIP users can apply for discounts.');
        }
    }

    private function deserializeMultiStepForm(Request $request): ?MultiStepForm
    {
        $multiStep = $request->cookies->get('multi-step');

        return $multiStep ? unserialize($multiStep, [MultiStepForm::class]) : null;
    }
}
