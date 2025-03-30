<?php

namespace App\Controller\Admin;

use App\Form\FormationType;
use App\Repository\FormationRepository;
use App\Repository\CategorieRepository;
use App\Entity\Formation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


/**
 * Contrôleur du back-office pour la gestion des formations
 * 
 *
 * @author arthurponcin
 */
#[Route('/admin/formation')]
class FormationController extends AbstractController
{
    private const TEMPLATE_FORMATION = 'admin/formation/index.html.twig';

    /**
     * @var FormationRepository
     */
    private FormationRepository $formationRepository;

    /**
     * @var CategorieRepository
     */
    private CategorieRepository $categorieRepository;

    /**
     * Constructeur
     *
     * @param FormationRepository $formationRepository
     * @param CategorieRepository $categorieRepository
     */
    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository)
    {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
    }

    /**
     * Affiche la liste des formations sans tri ni filtre
     *
     * @return Response
     */
    #[Route('/', name: 'admin_formation_index')]
    public function index(): Response
    {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::TEMPLATE_FORMATION, [
            'formations' => $formations,
            'categories' => $categories,
        ]);
    }

    
    /**
     * Permet d'éditer une formation
     *
     * @param Request $request
     * @param Formation $formation
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id<\d+>}/edit', name: 'admin_formation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'La formation a bien été modifiée.');
            $entityManager->flush();
            return $this->redirectToRoute('admin_formation_index');
        }

        return $this->render('admin/formation/edit.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    /**
     * Permet de supprimer une formation
     *
     * @param Request $request
     * @param Formation $formation
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id<\d+>}', name: 'admin_formation_delete', methods: ['POST'])]
    public function delete(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($formation);
            $this->addFlash('success', 'La formation a bien été supprimée.');
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_formation_index');
    }
    
    /**
     *Permet d'ajouter une formation
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/new', name: 'admin_formation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'La formation a bien été créée.');
            $entityManager->persist($formation);
            $entityManager->flush();

            return $this->redirectToRoute('admin_formation_index');
        }

        return $this->render('admin/formation/new.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }
    
    /**
     * Trie les formations selon un champ et un ordre donnés
     *
     * @param string $champ
     * @param string $ordre
     * @param string $table
     * @return Response
     */
    #[Route('/tri/{champ}/{ordre}/{table}', name: 'admin_formation_sort')]
    public function sort(string $champ, string $ordre, string $table = ''): Response
    {
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::TEMPLATE_FORMATION, [
            'formations' => $formations,
            'categories' => $categories,
        ]);
    }

    /**
     * Recherche les formations contenant une valeur dans un champ donné
     *
     * @param string $champ
     * @param Request $request
     * @param string $table
     * @return Response
     */
    #[Route('/recherche/{champ}/{table}', name: 'admin_formation_recherche')]
    public function findAllContain(string $champ, Request $request, string $table = ''): Response
    {
        $valeur = $request->get('recherche');
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::TEMPLATE_FORMATION, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'champ' => $champ,
            'table' => $table,
        ]);
    }

    /**
     * Affiche une formation en détail
     *
     * @param int $id
     * @return Response
     */
    #[Route('/{id<\d+>}', name: 'admin_formation_showone')]
    public function showOne(int $id): Response
    {
        $formation = $this->formationRepository->find($id);
        $categories = $this->categorieRepository->findAll();
        return $this->render('admin/formation/show.html.twig', [
            'formation' => $formation,
            'categories'=> $categories,
        ]);
    }   
}
