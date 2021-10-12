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
    public function premiereRoute(EntityManagerInterface $em)
    {
        $liste= $em->getRepository(Patient::class)->findAll();
        return $this->render('aff.json.twig', ['liste'=> $liste]);
    }

    /**
     * @param EntityManagerInterface $em
     * @param int $id
     * @return Response
     * This function display the patient's information by his id
     */
    #[Route(path: "/patient/{id}", methods: ['GET'])]
    public function secondeRoute(EntityManagerInterface $em, int $id)
    {
        $pat = $em->getRepository(Patient::class)->find($id);
        return $this->render('affichage.json.twig', ['patient' => $pat]);
        return new Response('Voici les informations du patient : ', $id);
    }

    #[Route(path: "/patientAnalyses/{id}", methods: ['GET'])]
    public function patientAnalyses(EntityManagerInterface $em, int $id)
    {
        $pat = $em->getRepository(Patient::class)->find($id);
        if($pat!=NULL){
            $ana = $em->getRepository(Analyse::class)->findBy(['patient'=> $id]);
            if ($ana!=NULL) {
                foreach ($ana as $a) {
                    return $this->render('analyseresult.json.twig', ['analyse' => $a]);
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
    public function troisiemeRoute(EntityManagerInterface $em, int $id)
    {
        $pat = $em->getRepository(Patient::class)->find($id);
        if($pat!=NULL){
            $ana = $em->getRepository(Analyse::class)->findBy(['patient'=> $id]);
            if ($ana!=NULL) {
                foreach ($ana as $a) {
                    $em->remove($a);
                }
                $em->remove($pat);
                $em->flush();
                return new Response('Patient et analyses supprimés');
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
    public function quatriemeRoute(Request $request, EntityManagerInterface $em, int $id)
    {
        $data = json_decode($request->getContent()); //retrieval of changed information in the postman

        $pat = $em->getRepository(Patient::class)->find($id);

        $pat->nomPatient = $data->nom; //Change informations on the database
        $em->flush();
        return $this->render('affichage.json.twig', ['patient' => $pat]);

    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param int $id
     * @return Response
     * This function allows to change the analyse result
     */
    #[Route(path: "/analyse/{id}", methods: ['PUT'])]
    public function analyseChange(Request $request, EntityManagerInterface $em, int $id)
    {
        $data = json_decode($request->getContent()); //retrieval of changed information in the postman

        $ana = $em->getRepository(Analyse::class)->find($id);

        $ana->result = $data->result; //Change informations on the database
        $em->flush();
        return $this->render('analyseresult.json.twig', ['analyse' => $ana]);

    }

    /**
     * @param EntityManagerInterface $em
     * @return Response
     * This function display the list of all analyses
     */
    #[Route(path: "/analyses")]
    public function listAnalyse(EntityManagerInterface $em)
    {
        $liste= $em->getRepository(Analyse::class)->findAll();
        return $this->render('analyses.json.twig', ['liste'=> $liste]);
    }

    /**
     * @param EntityManagerInterface $em
     * @param int $id
     * @return Response
     * This function displays the analyse informations with his id
     */
    #[Route(path: "/analyse/{id}", methods: ['GET'])]
    public function affAnalyseid(EntityManagerInterface $em, int $id)
    {
        $ana = $em->getRepository(Analyse::class)->find($id);
        return $this->render('analyseresult.json.twig', ['analyse' => $ana]);
    }

    /**
     * @param EntityManagerInterface $em
     * @param int $id
     * @return Response
     * Delete an analyse according to his id
     */
    #[Route(path: "/analyse/{id}", methods: ['DELETE'])]
    public function deleteAnalyseid(EntityManagerInterface $em, int $id)
    {
        $ana = $em->getRepository(Analyse::class)->find($id);
        $em->remove($ana);
        $em->flush();
        return new Response('Analyse supprimée');
    }
}