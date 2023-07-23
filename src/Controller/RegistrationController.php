<?php

namespace App\Controller;

use App\Form\RegistrationFormType;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class RegistrationController extends AbstractController
{
    /** @var  FormFactoryInterface */
    private $formFactory;

    /** @var  RouterInterface */
    private $router;

    /** @var UserManager */
    private $userManager;

    public function __construct(
        FormFactoryInterface $formFactory,
        RouterInterface      $router,
        UserManager          $userManager
    )
    {
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->userManager = $userManager;
    }

    /**
     * @Route("/register", name="register")
     */
    public function registerAction(Request $request)
    {
        $form = $this->createRegistrationForm($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->handleRegistrationFormSubmission($form, $request);
        }

        $view = $this->renderView('security/registration.html.twig', [
            'form' => $form->createView(),
        ]);

        return new Response($view);
    }

    private function createRegistrationForm(Request $request)
    {
        $form = $this->formFactory->create(RegistrationFormType::class);
        $form->handleRequest($request);

        return $form;
    }

    private function handleRegistrationFormSubmission(FormInterface $form, Request $request)
    {
        $data = $form->getData();
        $user = $this->userManager->register($data);
        $this->userManager->login($user, $request);
        $this->addFlash('success', 'You\'ve been registered successfully');

        return new RedirectResponse($this->router->generate('home'));
    }

}
