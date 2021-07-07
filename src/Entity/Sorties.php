<?php

namespace App\Entity;

use App\Repository\SortiesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SortiesRepository::class)
 * @ORM\Table(name="sorties")
 */
class Sorties
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Regex(
     *  pattern = "/^[a-zA-Z]+$/i",
     *  htmlPattern = "/^[a-zA-Z]+$/i",
     *  message="Le nom de la sortie ne doit contenir que des lettres"
     * )
     * @ORM\Column(type="string", length=30)
     */
    private $nom;

    /**
     * @ORM\Column(type="date")
     */
    private $datedebut;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Length(
     *     min=1,
     *     max=11,
     * )
     */
    private $duree;

    /**
     * @ORM\Column(type="date")
     */
    private $datecloture;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Length(
     *      min=1,
     *      max=20,
     * )
     */
    private $nbinscriptionsmax;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(
     *     min=5,
     *     max=500
     * )
     */
    private $descriptioninfos;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Length(
     *      min=1,
     *      max=11,
     * )
     */
    private $etatsortie;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private $urlPhoto;

    /**
     * @ORM\ManyToMany(targetEntity=Participant::class, inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="id_participant")
     * @ORM\JoinTable(name="inscriptions",
     *     joinColumns={@ORM\JoinColumn(name="sortiesNoSortie", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="participantsNoParticipant", referencedColumnName="id_participant")})
     */
    private $estInscrit;

    /**
     * @ORM\ManyToOne(targetEntity=Participant::class, inversedBy="orgaSortie")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="id_participant", onDelete="CASCADE")
     */
    private $organisateur;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="id")
     */
    private $siteOrganisateur;

    /**
     * @ORM\ManyToOne(targetEntity=Lieu::class, inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lieu;


    public function __construct()
    {
        $this->estInscrit = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDatedebut(): ?\DateTimeInterface
    {
        return $this->datedebut;
    }

    public function setDatedebut(\DateTimeInterface $datedebut): self
    {
        $this->datedebut = $datedebut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDatecloture(): ?\DateTimeInterface
    {
        return $this->datecloture;
    }

    public function setDatecloture(\DateTimeInterface $datecloture): self
    {
        $this->datecloture = $datecloture;

        return $this;
    }

    public function getNbinscriptionsmax(): ?int
    {
        return $this->nbinscriptionsmax;
    }

    public function setNbinscriptionsmax(int $nbinscriptionsmax): self
    {
        $this->nbinscriptionsmax = $nbinscriptionsmax;

        return $this;
    }

    public function getDescriptioninfos(): ?string
    {
        return $this->descriptioninfos;
    }

    public function setDescriptioninfos(?string $descriptioninfos): self
    {
        $this->descriptioninfos = $descriptioninfos;

        return $this;
    }

    public function getEtatsortie(): ?int
    {
        return $this->etatsortie;
    }

    public function setEtatsortie(?int $etatsortie): self
    {
        $this->etatsortie = $etatsortie;

        return $this;
    }

    public function getUrlPhoto(): ?string
    {
        return $this->urlPhoto;
    }

    public function setUrlPhoto(?string $urlPhoto): self
    {
        $this->urlPhoto = $urlPhoto;

        return $this;
    }

    /**
     * @return Collection|Participant[]
     */
    public function getEstInscrit(): Collection
    {
        return $this->estInscrit;
    }

    public function addEstInscrit(Participant $estInscrit): self
    {
        if (!$this->estInscrit->contains($estInscrit)) {
            $this->estInscrit[] = $estInscrit;
        }

        return $this;
    }

    public function removeEstInscrit(Participant $estInscrit): self
    {
        $this->estInscrit->removeElement($estInscrit);

        return $this;
    }

    public function getOrganisateur(): ?Participant
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?Participant $organisateur): self
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    public function getSiteOrganisateur(): ?Campus
    {
        return $this->siteOrganisateur;
    }

    public function setSiteOrganisateur(?Campus $siteOrganisateur): self
    {
        $this->siteOrganisateur = $siteOrganisateur;

        return $this;
    }

    public function __toString()
    {
        return (string)$this->getId();
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }
    

}
