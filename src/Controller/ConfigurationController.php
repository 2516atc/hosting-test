<?php

namespace App\Controller;

use App\Entity\DeployConfiguration;
use App\Form\Type\DeployConfigurationType;
use App\Repository\DeployConfigurationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/configuration')]
class ConfigurationController extends AbstractController
{
    function __construct(private readonly EntityManagerInterface $entityManager) { }

    #[Route('', name: 'configuration_all', methods: ['GET'])]
    public function all(DeployConfigurationRepository $deployConfigurationRepository): Response
    {
        $deployConfigurations = $deployConfigurationRepository->findAll();

        return $this->render('configuration.list.html.twig', [
            'configurations' => $deployConfigurations
        ]);
    }

    #[Route('/new', name: 'configuration_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $configuration = new DeployConfiguration();

        return $this->deployConfigurationForm($configuration, $request);
    }

    #[Route('/{id}', name: 'configuration_edit', methods: ['GET', 'POST'])]
    public function edit(DeployConfiguration $configuration, Request $request): Response
    {
        return $this->deployConfigurationForm($configuration, $request);
    }

    #[Route('/{id}/delete', name: 'configuration_delete', methods: ['GET', 'POST'])]
    public function delete(DeployConfiguration $configuration, Request $request): Response
    {
        if ($request->getMethod() === 'GET')
        {
            return $this->render('configuration.delete.html.twig', [
                'configuration' => $configuration
            ]);
        }

        $this->entityManager->remove($configuration);
        $this->entityManager->flush();

        return $this->redirectToRoute('configuration_all');
    }

    private function deployConfigurationForm(DeployConfiguration $configuration, Request $request): Response
    {
        $form = $this->createForm(DeployConfigurationType::class, $configuration);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $configuration = $form->getData();

            $this->entityManager->persist($configuration);
            $this->entityManager->flush();

            return $this->redirectToRoute('configuration_all');
        }

        return $this->render('configuration.form.html.twig', [
            'form' => $form,
            'title' => $configuration->getId() === null
                ? 'New Configuration'
                : 'Edit Configuration'
        ]);
    }
}
