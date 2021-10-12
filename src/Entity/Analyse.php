<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * Class Analyse
 *
 * @Entity
 * @ORM\Table(name="Analyse")
 */
class Analyse
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_analyse", type="integer")
     * @ORM\Id()
     */
    public int $idAnalyse;

    /**
     * @var string
     *
     * @ORM\Column(name="type_analyse", type="string")
     */
    public string $typeAnalyse;

    /**
     * @var string
     *
     * @ORM\Column(name="result", type="string")
     */
    public string $result;

    /**
     * @var Patient!null
     * @ORM\ManyToOne(targetEntity=Patient::class, fetch="EAGER")
     * @JoinColumn(name="id_pat", referencedColumnName="Id_patient")
     */
    public ?Patient $patient;

    public function __construct(int $idAnalyse, string $typeAnalyse, string $result, ?Patient $patient=null){
        $this->idAnalyse = $idAnalyse;
        $this->typeAnalyse = $typeAnalyse;
        $this->result = $result;
        $this->patient = $patient;
    }

}