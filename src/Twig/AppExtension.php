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
                return $interval->format('moins d\'une heure');
            } else {
                $stringEnd="s";
                return $interval->format('%h heure'.$stringEnd);
            }
        } elseif ($interval->days < 30) {
            if ($interval->days >1) {
                $stringEnd="s";
            }
            return $interval->format('%a jour'.$stringEnd);
        } elseif ($interval->days < 365) {
            return $interval->format('%m mois');
        } else {
            if ($interval->y > 1) {
                $stringEnd="s";
            }
            return $interval->format('%y an'.$stringEnd);
        }
    }
}