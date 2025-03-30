<?php

namespace App\Controller\Admin;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\PlaylistRepository;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur du back-office pour la gestion des playlists
 * 
 *
 * @author arthurponcin
 */
#[Route('/admin/playlist')]
class PlaylistController extends AbstractController {

    private const TEMPLATE_PLAYLIST = 'admin/playlist/index.html.twig';

    /**
     * @var CategorieRepository
     */
    private CategorieRepository $categorieRepository;

    /**
     * @var PlaylistRepository
     */
    private PlaylistRepository $playlistRepository;

    /**
     * @var FormationRepository
     */
    private FormationRepository $formationRepository;

    /**
     * Constructeur
     *
     * @param PlaylistRepository $playlistRepository
     * @param CategorieRepository $categorieRepository
     */
    public function __construct(PlaylistRepository $playlistRepository,
            CategorieRepository $categorieRepository,
            FormationRepository $formationRepository)
    {
        $this->formationRepository = $formationRepository;
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
    }

    /**
     *  Affiche toutes les playlists.
     *
     * @return Response
     */
    #[Route('/', name: 'admin_playlist_index', methods: ['GET'])]
    public function index(): Response {
        $categories = $this->categorieRepository->findAll();
        $playlists = $this->playlistRepository->findAll();
        $formations = $this->formationRepository->findAll();
        return $this->render(self::TEMPLATE_PLAYLIST, [
                    'playlists' => $playlists,
                    'categories' => $categories,
                    'formations' => $formations
        ]);
    }

    /**
    * Permet de créer une nouvelle playlist.
    *
    * @param Request $request
    * @param EntityManagerInterface $entityManager
    * @return Response
    */
    #[Route('/new', name: 'admin_playlist_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response {
        $playlist = new Playlist();
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'La playlist a bien été créée.');
            $entityManager->persist($playlist);
            $entityManager->flush();

            return $this->redirectToRoute('admin_playlist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/playlist/new.html.twig', [
                    'playlist' => $playlist,
                    'form' => $form,
        ]);
    }

    /**
    * Permet de modifier une playlist existante.
    *
    * @param Request $request
    * @param Playlist $playlist
    * @param EntityManagerInterface $entityManager
    * @return Response
    */
    #[Route('/{id<\d+>}/edit', name: 'admin_playlist_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Playlist $playlist, EntityManagerInterface $entityManager): Response {
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($playlist->getId());
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($playlist->getId());

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'La playlist a bien été modifiée.');
            $entityManager->flush();

            return $this->redirectToRoute('admin_playlist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/playlist/edit.html.twig', [
                    'playlist' => $playlist,
                    'form' => $form,
                    'playlistcategories' => $playlistCategories,
                    'playlistformations' => $playlistFormations,
                    'formationCount' => $playlist->getFormationCount()
        ]);
    }

    /**
    * Supprime une playlist.
    *
    * @param Request $request
    * @param Playlist $playlist
    * @param EntityManagerInterface $entityManager
    * @return Response
    */
    #[Route('/{id}', name: 'admin_playlist_delete', methods: ['POST'])]
    public function delete(Request $request, Playlist $playlist, EntityManagerInterface $entityManager): Response {
        if ($this->isCsrfTokenValid('delete' . $playlist->getId(), $request->getPayload()->get('_token'))) {
            $this->addFlash('success', 'La playlist a bien été supprimée.');
            $entityManager->remove($playlist);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_playlist_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
    * Trie les playlists selon un champ et un ordre donné.
    *
    * @param string $champ
    * @param string $ordre
    * @return Response
    */
    #[Route('/tri/{champ}/{ordre}', name: 'admin_playlists_sort')]
    public function sort($champ, $ordre): Response {
        if ($champ === "name") {
            $playlists = $this->playlistRepository->findAllOrderByName($ordre);
        } elseif ($champ === "formations") {
            $playlists = $this->playlistRepository->findAllOrderByFormationCount($ordre);
        } else {
            throw $this->createNotFoundException("Le critère de tri '$champ' est invalide.");
        }

        $categories = $this->categorieRepository->findAll();
        return $this->render(self::TEMPLATE_PLAYLIST, [
                    'playlists' => $playlists,
                    'categories' => $categories
        ]);
    }

    /**
    * Trie les playlists par nombre de formations.
    *
    * @param string $ordre
    * @return Response
    */
    #[Route('/tri/formations/{ordre}', name: 'admin_playlists_sortByFormationCount')]
    public function sortByFormationCount(string $ordre): Response {
        $playlists = $this->playlistRepository->findAllOrderByFormationCount($ordre);
        $categories = $this->categorieRepository->findAll();

        return $this->render(self::TEMPLATE_PLAYLIST, [
                    'playlists' => $playlists,
                    'categories' => $categories
        ]);
    }

    /**
    * Filtre les playlists selon un champ et une valeur.
    *
    * @param string $champ
    * @param Request $request
    * @param string $table
    * @return Response
    */
    #[Route('/recherche/{champ}/{table}', name: 'admin_playlists_findallcontain')]
    public function findAllContain($champ, Request $request, $table = ""): Response {
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::TEMPLATE_PLAYLIST, [
                    'playlists' => $playlists,
                    'categories' => $categories,
                    'valeur' => $valeur,
                    'table' => $table
        ]);
    }

    /**
    * Affiche le détail d'une playlist.
    *
    * @param int $id
    * @return Response
    */
    #[Route('/{id}', name: 'admin_playlist_showone')]
    public function showOne($id): Response {
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        return $this->render('admin/playlist/show.html.twig', [
                    'playlist' => $playlist,
                    'playlistcategories' => $playlistCategories,
                    'playlistformations' => $playlistFormations,
                    'formationCount' => $playlist->getFormationCount()
        ]);
    }
}
