<?php

namespace App\Controller;

use App\Entity\Analyse;
use App\Entity\Patient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Controller extends AbstractController
{
    /**
     * @param EntityManagerInterface $em
     * @return Response
     * This function display the list of patients on json format
     */
    #[Route(path: "/patients")]
    public function affichageListePatients(EntityManagerInterface $em)
    {
        $listepatients= $em->getRepository(Patient::class)->findAll();
        if($listepatients!=NULL){
        return $this->render('listePatients.json.twig', ['liste'=> $listepatients]);
        }else{
            return new Response("Pas de patients dans la base");
        }
    }

    /**
     * @param EntityManagerInterface $em
     * @param int $id
     * @return Response
     * This function display the patient's information by his id
     */
    #[Route(path: "/patient/{id}", methods: ['GET'])]
    public function affichageInformationsUnPatient(EntityManagerInterface $em, int $id)
    {
        $patient = $em->getRepository(Patient::class)->find($id);
        if($patient!=NULL) {
            return $this->render('informationsUnPatient.json.twig', ['patient' => $patient]);
        }else{
            return new Response("Pas de patient avec cet identifiant");
        }
    }

    /**
     * @param EntityManagerInterface $em
     * @param int $id
     * @return Response|void
     * This function display analyses of a patient
     */
    #[Route(path: "/patientAnalyses/{id}", methods: ['GET'])]
    public function analysesPatient(EntityManagerInterface $em, int $id)
    {
        $patient = $em->getRepository(Patient::class)->find($id);
        if($patient!=NULL){
            $analyses = $em->getRepository(Analyse::class)->findBy(['patient'=> $id]);
            if ($analyses!=NULL) {
                foreach ($analyses as $analyse) {
                    return $this->render('analysesPatient.json.twig', ['patient'=> $id, 'analyses' => $analyses]);
                }
            } else {
                return new Response("Pas d'analyses pour ce patient");
            }
        }else{
            return new Response("Pas de patient pour cet identifiant");
        }
    }

    /**
     * @param EntityManagerInterface $em
     * @param int $id
     * @return Response
     * This function delete a patient and his analyses from the database with json
     */
    #[Route(path: "/patient/{id}", methods: ['DELETE'])]
    public function suppressionPatient(EntityManagerInterface $em, int $id)
    {
        $patient = $em->getRepository(Patient::class)->find($id);
        if($patient!=NULL){
            $analyses = $em->getRepository(Analyse::class)->findBy(['patient'=> $id]);
            if ($analyses!=NULL) {
                foreach ($analyses as $analyse) {
                    $em->remove($analyse);
                }
                $em->remove($patient);
                $em->flush();
                return new Response('Patient et ses analyses supprim??es');
            } else {
                return new Response("Pas d'analyses pour ce patient");
            }
        }else{
            return new Response("Pas de patient pour cet identifiant");
        }
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param int $id
     * @return Response
     * This function modify information of the patient
     */
    #[Route(path: "/patient/{id}", methods: ['PUT'])]
    public function modificationPatient(Request $request, EntityManagerInterface $em, int $id)
    {
        $data = json_decode($request->getContent()); //retrieval of changed information in the postman
        $patient = $em->getRepository(Patient::class)->find($id);

        if($patient!=NULL) {
            $patient->nomPatient = $data->nomPatient;
            $em->flush();
            return $this->render('creationPatient.json.twig', ['patient' => $patient]);
        }else{
            return new Response("Pas de patient pour cet identifiant, vous ne pouvez pas modifier un patient qui n'existe pas !");
        }
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param int $id
     * @return Response
     * This function allows to change the analyse result
     */
    #[Route(path: "/analyse/{id}", methods: ['PUT'])]
    public function modificationAnalyse(Request $request, EntityManagerInterface $em, int $id)
    {
        $data = json_decode($request->getContent()); //retrieval of changed information in the postman
        $analyse = $em->getRepository(Analyse::class)->find($id);
        if($analyse!=NULL) {
            $analyse->typeAnalyse = $data->type;
            $analyse->result = $data->result;
            $idPat = $data->idPatient;
            $patient = $em->getRepository(Patient::class)->find($idPat);
            if($patient!=NULL) {
                $analyse->patient = $patient;
                $em->flush();
                return $this->render('creationAnalyse.json.twig', ['analyse' => $analyse]);
            }else{
                return new Response("Pas de patient avec cet identifiant");
            }
        }else{
            return new Response("Pas d'analyse avec cet identifiant");
        }
    }

    /**
     * @param EntityManagerInterface $em
     * @return Response
     * This function display the list of all analyses
     */
    #[Route(path: "/analyses")]
    public function listeAnalyses(EntityManagerInterface $em)
    {
        $liste= $em->getRepository(Analyse::class)->findAll();
        if($liste!=NULL) {
            return $this->render('analyses.json.twig', ['liste' => $liste]);
        }else{
            return new Response("Pas d'analyses dans la table");
        }
    }

    /**
     * @param EntityManagerInterface $em
     * @param int $id
     * @return Response
     * This function displays the analyse informations with his id
     */
    #[Route(path: "/analyse/{id}", methods: ['GET'])]
    public function affichageUneAnalyse(EntityManagerInterface $em, int $id)
    {
        $analyse = $em->getRepository(Analyse::class)->find($id);
        if($analyse!=NULL) {
            return $this->render('analyseresult.json.twig', ['analyse' => $analyse]);
        }else{
            return new Response("Pas d'analyse avec cet identifiant");
        }
    }

    /**
     * @param EntityManagerInterface $em
     * @param int $id
     * @return Response
     * Delete an analyse according to his id
     */
    #[Route(path: "/analyse/{id}", methods: ['DELETE'])]
    public function suppressionAnalyse(EntityManagerInterface $em, int $id)
    {
        $analyse = $em->getRepository(Analyse::class)->find($id);
        if($analyse!=NULL) {
            $em->remove($analyse);
            $em->flush();
            return new Response('Analyse supprim??e');
        }else{
            return new Response('Analyse introuvable');
        }
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     * This function allows to create a new analysis for a patient
     */
    #[Route(path: "/creationAnalyse", methods: ['POST'])]
    public function creationAnalyse(Request $request, EntityManagerInterface $em)
    {
        $data = json_decode($request->getContent());
        $type = $data->type;
        $result=$data->result;
        $idPat=$data->idPatient;
        $patient = $em->getRepository(Patient::class)->find($idPat);
        if($patient!=NULL) {
            $analyse = new Analyse($type, $result, $patient);
            $em->persist($analyse);
            $em->flush();
            return $this->render('creationAnalyse.json.twig', ['analyse' => $analyse]);
        }else{
            return new Response('Pas de patient avec cet identifiant');
        }
    }

    #[Route(path: "/creationPatient", methods: ['POST'])]
    public function creationPatient(Request $request, EntityManagerInterface $em)
    {
        $data = json_decode($request->getContent());
        $nomPatient = $data->nomPatient;
        $patient = new Patient($nomPatient);
        $em->persist($patient);
        $em->flush();
        return $this->render('creationPatient.json.twig', ['patient' => $patient]);
    }
}