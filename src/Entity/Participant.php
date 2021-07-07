<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ParticipantRepository::class)
 * @ORM\Table(name="participant")
 * @UniqueEntity(fields={"pseudo"}, message="Pseudo déjà existant.")
 * @UniqueEntity(fields={"mail"}, message="Mail déjà existant.")
 */
class Participant implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $idParticipant;

    /**
     * @ORM\Column(type="string", length=30, unique=true)
     * @Assert\NotBlank()
     * @Assert\Regex(
     *  pattern = "/^[a-zA-Z0-9]([._-](?![._-])|[a-zA-Z0-9])+$/i",
     *  htmlPattern = "/^[a-zA-Z0-9]([._-](?![._-])|[a-zA-Z0-9])+$/i",
     *  message="Votre pseudo ne doit contenir que des caractères alphanumériques",
     *  message="Le point (.), underscore (_), ou tiret (-) ne doit pas être le premier ou le dernier caractère"
     * )
     */
    private $pseudo;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex(
     *  pattern = "/^[a-zA-Z]+$/i",
     *  htmlPattern = "/^[a-zA-Z]+$/i",
     *  message="Votre prénom ne doit contenir que des lettres"
     * )
     * @ORM\Column(type="string", length=30)
     */
    private $prenom;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex(
     *  pattern = "/^[a-zA-Z]+$/i",
     *  htmlPattern = "/^[a-zA-Z]+$/i",
     *  message="Votre nom ne doit contenir que des lettres"
     * )
     * @ORM\Column(type="string", length=30)
     */
    private $nom;

    /**
     * @Assert\Regex(
     *  pattern = "/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/i",
     *  htmlPattern = "/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/i",
     *  message="Numéro de téléphone invalide"
     * )
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $telephone;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex(
     *  pattern = "/^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/i",
     *  htmlPattern = "/^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/i"
     * )
     * @ORM\Column(type="string", length=50, unique=true)
     * @Assert\Email(message = "'{{ value }}' n'est pas valide.")
     */
    private $mail;

    /**
     * @Assert\Length( min="5", max="100")
     * @ORM\Column(type="string")
     */
    private $mot_de_passe;

    /**
     * @ORM\Column(type="smallint")
     */
    private $administrateur;

    /**
     * @ORM\Column(type="smallint")
     */
    private $actif;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\ManyToMany(targetEntity=Sorties::class, mappedBy="estInscrit", orphanRemoval=true)
     */
    private $sorties;

    /**
     * @ORM\OneToMany(targetEntity=Sorties::class, mappedBy="organisateur", orphanRemoval=true)
     */
    private $orgaSortie;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="participant")
     * @ORM\JoinColumn(nullable=false)
     */
    private $campus;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photoDeProfil;

    public function __construct()
    {
        $this->sorties = new ArrayCollection();
        $this->orgaSortie = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->idParticipant;
    }

    public function setId($id): self
    {
        $this->idParticipant = $id;

        return $this;
    }


    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
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

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): self
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->pseudo;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->mot_de_passe;
    }

    public function setPassword(string $password): self
    {
        $this->mot_de_passe = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Sorties[]
     */

    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSorty(Sorties $sorty): self
    {
        if (!$this->sorties->contains($sorty)) {
            $this->sorties[] = $sorty;
            $sorty->addEstInscrit($this);

        }

        return $this;
    }

    public function removeSorty(Sorties $sorty): self
    {
        if ($this->sorties->removeElement($sorty)) {
            $sorty->removeEstInscrit($this);
        }

        return $this;
    }

    /**
     * @return Collection|Sorties[]
     */
    public function getOrgaSortie(): Collection
    {
        return $this->orgaSortie;
    }

    public function addOrgaSortie(Sorties $orgaSortie): self
    {
        if (!$this->orgaSortie->contains($orgaSortie)) {
            $this->orgaSortie[] = $orgaSortie;
            $orgaSortie->setOrganisateur($this);
        }

        return $this;
    }

    public function removeOrgaSortie(Sorties $orgaSortie): self
    {
        if ($this->orgaSortie->removeElement($orgaSortie)) {
            // set the owning side to null (unless already changed)
            if ($orgaSortie->getOrganisateur() === $this) {
                $orgaSortie->setOrganisateur(null);
            }
        }

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    public function __toString()
    {
        return (string)$this->getId();
    }

    public function getPhotoDeProfil(): ?string
    {
        return $this->photoDeProfil;
    }

    public function setPhotoDeProfil(?string $photoDeProfil): self
    {
        $this->photoDeProfil = $photoDeProfil;

        return $this;
    }

}
