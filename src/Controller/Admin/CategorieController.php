<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur du back-office pour la gestion des catégories
 * 
 *
 * @author arthurponcin
 */
#[Route('admin/categorie')]
class CategorieController extends AbstractController {

    private const TEMPLATE_CATEGORIE = 'admin/categorie/index.html.twig';

    /**
     * @var CategorieRepository
     */
    private CategorieRepository $categorieRepository;

    /**
     * Constructeur
     *
     * @param CategorieRepository $categorieRepository
     */
    public function __construct(CategorieRepository $categorieRepository) {
        $this->categorieRepository = $categorieRepository;
    }

    #[Route('/', name: 'admin_categorie_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em): Response {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existing = $this->categorieRepository->findOneBy(['name' => $categorie->getName()]);
            if ($existing) {
                $this->addFlash('error', 'Cette catégorie existe déjà.');
            } else {
                $em->persist($categorie);
                $em->flush();
                $this->addFlash('success', 'Catégorie ajoutée avec succès.');
                return $this->redirectToRoute('admin_categorie_index');
            }
        }

        return $this->render(self::TEMPLATE_CATEGORIE, [
                    'categories' => $this->categorieRepository->findAll(),
                    'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'admin_categorie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute(self::TEMPLATE_CATEGORIE, [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/categorie/new.html.twig', [
                    'categorie' => $categorie,
                    'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_categorie_show', methods: ['GET'])]
    public function show(Categorie $categorie): Response {
        return $this->render('admin/categorie/show.html.twig', [
                    'categorie' => $categorie,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_categorie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Catégorie modifiée.');

            return $this->redirectToRoute('admin_categorie_index');
        }

        return $this->render('admin/categorie/edit.html.twig', [
                    'categorie' => $categorie,
                    'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_categorie_delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response {
        if ($this->isCsrfTokenValid('delete' . $categorie->getId(), $request->getPayload()->get('_token'))) {
            if (count($categorie->getFormations()) === 0) {
                $entityManager->remove($categorie);
                $entityManager->flush();
                $this->addFlash('success', 'Catégorie supprimée.');
            } else {
                $this->addFlash('error', 'Impossible de supprimer cette catégorie car elle contient des formations.');
            }
        }

        return $this->redirectToRoute('admin_categorie_index');
    }
}
