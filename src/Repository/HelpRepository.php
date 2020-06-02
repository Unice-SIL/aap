<?php


namespace App\Repository;


use App\Entity\Help;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class HelpRepository
 * @package App\Repository
 */
class HelpRepository
{
    /**
     * @var array
     */
    private $helps = [];

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * HelpRepository constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;

        foreach (json_decode(file_get_contents('../var/data/help.json'), true) as $h)
        {
            $help = new Help();
            $help->setLabel($h['label'])
                ->setTemplate($h['template'])
                ->setKeywords($h['keywords']);
            $this->helps[] = $help;
        }

    }

    /**
     * @param string $term
     * @return array
     */
    public function searchByKeyWord(string $term)
    {
        if(empty($term))
        {
            return [];
        }

        return array_filter($this->helps, function (Help $help) use ($term){
            $transliterator = \Transliterator::create('NFD; [:Nonspacing Mark:] Remove; NFC');

            $term = $transliterator->transliterate($term);
            $label = $transliterator->transliterate($this->translator->trans($help->getLabel()));

            // Search in keywords
            $score = 0;
            foreach ($help->getKeywords() as $keyword)
            {
                $keyword = $transliterator->transliterate($this->translator->trans("app.keyword.{$keyword}"));
                $keywords = explode(',', $keyword);
                foreach ($keywords as $k)
                {

                    if(preg_match("/{$term}/i", trim($k)) === 1)
                    {
                        if(strlen($term) > strlen($keyword))
                        {
                            $score = 2;
                        }
                        else
                        {
                            $score = 1 + (strlen($term) / strlen($keyword));
                        }
                    }

                }
            }
            $help->setScore($score);


            // Search in label
            if(preg_match("/{$term}/i", $label) === 1)
            {
                $help->setScore($help->getScore() + 1);
            }

            return $help->getScore() > 0;
        });
    }
}