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
        $stringEnd=""; //french plurals management
        if ($interval->days < 1) {
            $hours = $interval->h + $interval->i / 60;

            if ($hours < 1) {
                return $interval->format('Il y a moins d\'une heure');
            } else {
                $stringEnd="s";
                return $interval->format('Il y a %h heure'.$stringEnd);
            }
        } elseif ($interval->days < 30) {
            if ($interval->days >1) {
                $stringEnd="s";
            }
            return $interval->format('Il y a %a jour'.$stringEnd);
        } elseif ($interval->days < 365) {
            return $interval->format('Il y a %m mois');
        } else {
            if ($interval->y > 1) {
                $stringEnd="s";
            }
            return $interval->format('Il y a %y an'.$stringEnd);
        }
    }
}