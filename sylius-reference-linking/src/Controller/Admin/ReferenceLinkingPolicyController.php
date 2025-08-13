<?php

namespace Sylius\ReferenceLinking\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\ReferenceLinking\Entity\ReferenceLinkingPolicy;
use Sylius\ReferenceLinking\Form\Type\ReferenceLinkingPolicyType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/reference-linking')]
final class ReferenceLinkingPolicyController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[Route(path: '', name: 'app_admin_reference_linking_index')]
    public function index(): Response
    {
        $policies = $this->entityManager->getRepository(ReferenceLinkingPolicy::class)->findAll();
        return $this->render('admin/reference_linking/index.html.twig', [
            'policies' => $policies,
        ]);
    }

    #[Route(path: '/{referenceType}', name: 'app_admin_reference_linking_edit')]
    public function edit(Request $request, string $referenceType): Response
    {
        $policy = $this->entityManager->find(ReferenceLinkingPolicy::class, $referenceType);
        if (!$policy) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(ReferenceLinkingPolicyType::class, $policy);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('app_admin_reference_linking_index');
        }

        return $this->render('admin/reference_linking/edit.html.twig', [
            'form' => $form->createView(),
            'policy' => $policy,
        ]);
    }
}

