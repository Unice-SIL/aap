<?php


namespace App\Controller\Admin;


use App\Repository\DictionaryContentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DictionaryController
 * @package App\Controller\Admin
 * @Route("/dictionary", name="dictionary.")
 */
class DictionaryController extends AbstractController
{

    /**
     * @param DictionaryContentRepository $dictionaryContentRepository
     * @return Response
     * @Route(name="index", methods={"GET"})
     */
    public function index(DictionaryContentRepository $dictionaryContentRepository)
    {
        return $this->render('dictionary/index.html.twig', [
            'dictionaries' => $dictionaryContentRepository->findAll()
        ]);
    }
}