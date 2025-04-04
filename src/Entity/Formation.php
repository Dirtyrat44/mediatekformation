<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant une formation
 *
 * Une formation est liée à une playlist, peut appartenir à plusieurs catégories
 *
 */
#[ORM\Entity(repositoryClass: FormationRepository::class)]
#[UniqueEntity('title')]
class Formation
{
    /**
     * Début de chemin vers les images
     */
    private const CHEMIN_IMAGE = "https://i.ytimg.com/vi/";

    /**
     * Identifiant unique de la formation
     *
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

     /**
     * Date de publication de la formation
     *
     * @var \DateTimeInterface|null
     */
    #[Assert\NotNull(message: "La date de publication est requise.")]
    #[Assert\LessThanOrEqual('today', message: "La date ne peut pas être postérieure à aujourd’hui.")]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $publishedAt = null;

      /**
     * Titre de la formation
     *
     * @var string|null
     */
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $title = null;

    /**
     * Description de la formation
     *
     * @var string|null
     */
    #[Assert\Length(
        max: 1000,
        maxMessage: "La description ne doit pas dépasser {{ limit }} caractères."
    )]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * Identifiant YouTube de la vidéo
     *
     * @var string|null
     */
    #[Assert\NotBlank(message : 'Ce champ est requis')]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9_-]{11}$/',
        message: 'L’ID de la vidéo doit être un identifiant YouTube valide (11 caractères).'
    )]
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $videoId = null;

     /**
     * Playlist associée à la formation
     *
     * @var Playlist|null
     */
    #[Assert\NotNull(message: "La playlist est requise.")]
    #[ORM\ManyToOne(inversedBy: 'formations')]
    private ?Playlist $playlist = null;

    /**
     * @var Collection<int, Categorie>
     */
    #[ORM\ManyToMany(targetEntity: Categorie::class, inversedBy: 'formations')]
    private Collection $categories;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeInterface $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Retourne la date de publication au format string
     *
     * @return string
     */
    public function getPublishedAtString(): string
    {
        if ($this->publishedAt == null) {
            return "";
        }
        return $this->publishedAt->format('d/m/Y');
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getVideoId(): ?string
    {
        return $this->videoId;
    }

    public function setVideoId(?string $videoId): static
    {
        $this->videoId = $videoId;

        return $this;
    }

    /**
     * Retourne l'URL de la miniature
     *
     * @return string|null
     */
    public function getMiniature(): ?string
    {
        return self::CHEMIN_IMAGE . $this->videoId . "/default.jpg";
    }

    /**
     * Retourne l'URL de la miniature
     *
     * @return string|null
     */
    public function getPicture(): ?string
    {
        return self::CHEMIN_IMAGE . $this->videoId . "/hqdefault.jpg";
    }

    public function getPlaylist(): ?playlist
    {
        return $this->playlist;
    }

    public function setPlaylist(?Playlist $playlist): static
    {
        $this->playlist = $playlist;

        return $this;
    }

    /**
     * @return Collection<int, Categorie>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * Ajoute une catégorie à la formation
     *
     * @param Categorie $category
     * @return static
     */
    public function addCategory(Categorie $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addFormation($this); // Synchro inverse
        }

        return $this;
    }

    /**
     * Supprime une catégorie de la formation
     *
     * @param Categorie $category
     * @return static
     */
    public function removeCategory(Categorie $category): static
    {
        $this->categories->removeElement($category);
        $category->removeFormation($this); // Synchro inverse

        return $this;
    }
}
