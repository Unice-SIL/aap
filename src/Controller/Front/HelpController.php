<?php


namespace App\Controller\Front;


use App\Entity\Help;
use App\Repository\HelpRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HelpController
 * @package App\Controller
 * @Route("/help", name="help.")
 */
class HelpController extends AbstractController
{

    /**
     * @Route("/search", name="search", methods={"GET"})
     * @param Request $request
     * @param HelpRepository $helpRepository
     * @return Response
     */
    public function search(Request $request, HelpRepository $helpRepository)
    {
        $terms = $request->query->get('terms');
        //$terms = explode(' ', $terms);
        $terms = preg_split("/[\s,\']+/", $terms);
        $helps = [];

        // Search helps for each term
        foreach($terms as $term)
        {
            // Search only for terms over 2 chars
            if(strlen($term) <= 2)
            {
                continue;
            }

            // Search for term
            $h =  $helpRepository->searchByKeyWord($term);

            /** @var Help $help */
            foreach ($h as $help)
            {
                if(!array_key_exists($help->getLabel(), $helps))
                {
                    $helps[$help->getLabel()] = clone $help;
                }
                else
                {
                    $score = $helps[$help->getLabel()]->getScore() + $help->getScore();
                    $helps[$help->getLabel()]->setScore($score);
                }
            }
        }

        // Sort results by high score and label
        usort($helps, function (Help $a, Help $b){
            return $a->getScore() >= $b->getScore() ? -1 : 1;
        });

        // Keep only 6 high score
        $moreHelps = array_slice($helps, 4);
        $helps = array_slice($helps, 0, 4);

        return $this->render('partial/modal/_help_results.html.twig', [
            'helps' => $helps,
            'moreHelps' => $moreHelps
        ]);
    }

    /**
     * @Route("/show/{locale}/{template}", name="show", methods={"GET"}, requirements={"template"=".+"})
     * @param Request $request
     * @param string $locale
     * @param string $template
     * @return Response
     */
    public function show(Request $request, string $locale = 'fr', string $template = '')
    {
        $template = "help/{$locale}/{$template}.html.twig";
        return $this->render($template);
    }

}