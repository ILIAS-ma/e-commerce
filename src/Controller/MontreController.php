<?php

namespace App\Controller;

use App\Entity\Montre;
use App\Form\MontreForm;
use App\Repository\MontreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/montre')]
#[IsGranted('ROLE_ADMIN')]
final class MontreController extends AbstractController
{
    #[Route('/', name: 'app_montre_index', methods: ['GET'])]
    public function index(MontreRepository $montreRepository): Response
    {
        return $this->render('montre/index.html.twig', [
            'montres' => $montreRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_montre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MontreRepository $montreRepository, SluggerInterface $slugger, FormFactoryInterface $formFactory): Response
    {
        $montre = new Montre();
        $form = $formFactory->create(MontreForm::class, $montre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/images/montres',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $montre->setImage($newFilename);
            }

            $montreRepository->save($montre, true);

            return $this->redirectToRoute('app_montre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('montre/new.html.twig', [
            'montre' => $montre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_montre_show', methods: ['GET'])]
    public function show(Montre $montre): Response
    {
        return $this->render('montre/show.html.twig', [
            'montre' => $montre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_montre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Montre $montre, MontreRepository $montreRepository, SluggerInterface $slugger, FormFactoryInterface $formFactory): Response
    {
        $form = $formFactory->create(MontreForm::class, $montre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/images/montres',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $montre->setImage($newFilename);
            }

            $montreRepository->save($montre, true);

            return $this->redirectToRoute('app_montre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('montre/edit.html.twig', [
            'montre' => $montre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_montre_delete', methods: ['POST'])]
    public function delete(Request $request, Montre $montre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $montre->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($montre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_montre_index', [], Response::HTTP_SEE_OTHER);
    }
}
