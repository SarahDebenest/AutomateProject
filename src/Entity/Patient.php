<?php

namespace App\Entity;

use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

/**
 * Class Patient
 *
 * @Entity
 * @ORM\Table(name="Patient")
 */
class Patient
{
    /**
     * @var int
     * @ORM\Column(name="Id_patient", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     */
    public int $idPatient;

    /**
     * @var string
     * @ORM\Column(name="nom_patient", type="string")
     */
    public string $nomPatient;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity=Analyse::class, mappedBy="patient", fetch="EAGER")
     */
    public Collection $listeAnalyse;

    /**
     * @param string $nomPatient
     */
    public function __construct(string $nomPatient){
        $this->nomPatient = $nomPatient;
        $this->listeAnalyse=new ArrayCollection();
    }
}