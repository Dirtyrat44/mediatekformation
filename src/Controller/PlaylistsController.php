<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of PlaylistsController
 *
 * @author emds
 */
class PlaylistsController extends AbstractController
{

    private const ORDER_ASC = 'ASC';
    private const TEMPLATE_PLAYLISTS = "pages/playlists.html.twig";
    private const TEMPLATE_PLAYLIST = "pages/playlist.html.twig";

    /**
     *
     * @var PlaylistRepository
     */
    private playlistRepository $playlistRepository;

    /**
     *
     * @var FormationRepository
     */
    private FormationRepository $formationRepository;

    /**
     *
     * @var CategorieRepository
     */
    private CategorieRepository $categorieRepository;
    
    /**
     * Constructeur
     *
     * @param PlaylistRepository $playlistRepository
     * @param CategorieRepository $categorieRepository
     * @param FormationRepository $formationRespository
     */
    public function __construct(
        PlaylistRepository $playlistRepository,
        CategorieRepository $categorieRepository,
        FormationRepository $formationRespository
            ) {
                $this->playlistRepository = $playlistRepository;
                $this->categorieRepository = $categorieRepository;
                $this->formationRepository = $formationRespository;
            }

    /**
     * Affiche les playlists
     * 
     * @Route("/playlists", name="playlists")
     * @return Response
     */
    #[Route('/playlists', name: 'playlists')]
    public function index(): Response
    {
        $playlists = $this->playlistRepository->findAllOrderByName(self::ORDER_ASC);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::TEMPLATE_PLAYLISTS, [
                    'playlists' => $playlists,
                    'categories' => $categories
        ]);
    }

    /**
     * Trie les playlists selon champ et ordre
     *
     * @param type $champ
     * @param type $ordre
     * @return Response
     * @throws type
     */
    #[Route('/playlists/tri/{champ}/{ordre}', name: 'playlists.sort')]
    public function sort($champ, $ordre): Response
    {
        if ($champ === "name") {
            $playlists = $this->playlistRepository->findAllOrderByName($ordre);
        } elseif ($champ === "formations") { // Ajout du tri par formations
            $playlists = $this->playlistRepository->findAllOrderByFormationCount($ordre);
        } else {
            throw $this->createNotFoundException("Le critère de tri '$champ' est invalide.");
        }

        $categories = $this->categorieRepository->findAll();
        return $this->render(self::TEMPLATE_PLAYLISTS, [
                    'playlists' => $playlists,
                    'categories' => $categories
        ]);
    }
    
    /**
     * Trie les playlists par nombre de formations
     *
     * @param string $ordre
     * @return Response
     */
    #[Route('/playlists/tri/formations/{ordre}', name: 'playlists.sortByFormationCount')]
    public function sortByFormationCount(string $ordre): Response
    {
        $playlists = $this->playlistRepository->findAllOrderByFormationCount($ordre);
        $categories = $this->categorieRepository->findAll();

        return $this->render(self::TEMPLATE_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    /**
     * Filtre les playlists par un champ
     *
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    #[Route('/playlists/recherche/{champ}/{table}', name: 'playlists.findallcontain')]
    public function findAllContain($champ, Request $request, $table = ""): Response
    {
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::TEMPLATE_PLAYLISTS, [
                    'playlists' => $playlists,
                    'categories' => $categories,
                    'valeur' => $valeur,
                    'table' => $table
        ]);
    }

    /**
     * Affiche une playlist en détail
     *
     * @param type $id
     * @return Response
     */
    #[Route('/playlists/playlist/{id}', name: 'playlists.showone')]
    public function showOne($id): Response
    {
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        return $this->render(self::TEMPLATE_PLAYLIST, [
                    'playlist' => $playlist,
                    'playlistcategories' => $playlistCategories,
                    'playlistformations' => $playlistFormations,
                    'formationCount' => $playlist->getFormationCount()
        ]);
    }
}
