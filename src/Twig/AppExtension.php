<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('formattedElapsedTime', [$this, 'formatElapsedTime']),
        ];
    }

    public function formatElapsedTime($createdAt)
    {
        $currentDate = new \DateTime();
        $interval = $currentDate->diff($createdAt);

        if ($interval->days < 1) {
            return "Il y a 1 jour";
        } elseif ($interval->days < 30) {
            return $interval->format('Il y a %a jour%s');
        } elseif ($interval->days < 365) {
            return $interval->format('Il y a %m mois');
        } else {
            return $interval->format('Il y a %y an%s');
        }
    }
}