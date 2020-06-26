<?php


namespace App\Controller\Admin;


use App\Entity\Dictionary;
use App\Form\Dictionary\DictionaryType;
use App\Manager\Dictionary\DictionaryManagerInterface;
use App\Repository\DictionaryContentRepository;
use App\Repository\DictionaryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DictionaryController
 * @package App\Controller\Admin
 * @Route("/dictionary", name="dictionary.")
 */
class DictionaryController extends AbstractController
{

    /**
     * @param DictionaryRepository $dictionaryRepository
     * @return Response
     * @Route(name="index", methods={"GET"})
     */
    public function index(DictionaryRepository $dictionaryRepository)
    {
        return $this->render('dictionary/index.html.twig', [
            'dictionaries' => $dictionaryRepository->findAll()
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     * @param Request $request
     * @param DictionaryManagerInterface $dictionaryManager
     * @param TranslatorInterface $translator
     */
    public function new(Request $request, DictionaryManagerInterface $dictionaryManager, TranslatorInterface $translator)
    {
        $dictionary = $dictionaryManager->create();
        $form = $this->createForm(DictionaryType::class, $dictionary);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $dictionaryManager->save($dictionary);

            $this->addFlash('success', $translator->trans('app.flash_message.create_success', [
                '%item%' => $dictionary->getName()
            ]));

            return $this->redirectToRoute('app.admin.dictionary.show', [
                'id' => $dictionary->getId()
            ]);
        }

        return $this->render('dictionary.new.html.twig', [
            'form' => $form->createView(),
            'dictionary' => $dictionary
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     * @param Dictionary $dictionary
     * @param Request $request
     * @param DictionaryManagerInterface $dictionaryManager
     * @param TranslatorInterface $translator
     * @return RedirectResponse|Response
     */
    public function edit(Dictionary $dictionary, Request $request, DictionaryManagerInterface $dictionaryManager, TranslatorInterface $translator)
    {
        $form = $this->createForm(DictionaryType::class, $dictionary);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $dictionaryManager->update($dictionary);

            $this->addFlash('success', $translator->trans('app.flash_message.edit_success', [
                '%item%' => $dictionary->getName()
            ]));

            return $this->redirectToRoute('app.admin.dictionary.show', [
                'id' => $dictionary->getId()
            ]);
        }

        return $this->render('dictionary.edit.html.twig', [
            'form' => $form->createView(),
            'dictionary' => $dictionary
        ]);
    }

    /**
     * @Route("/{id}/show", name="show", methods={"GET"})
     * @param Dictionary $dictionary
     * @param Request $request
     * @return Response
     */
    public function show(Dictionary $dictionary, Request $request)
    {
        return $this->render('dictionary.show.html.twig', [
            'dictionary' => $dictionary
        ]);
    }
}