<?php

namespace App\Cells;

use CodeIgniter\View\Cells\Cell;

class ContainerCell extends Cell
{
    // Par défaut, le fond est blanc, mais on peut le changer
    public $bgColor = 'bg-white';

    // Le contenu HTML à injecter
    public $enfant = '';

    // Les classes de padding standardisées pour tout le site
    // Tu peux modifier ces valeurs ici pour impacter TOUTES les pages d'un coup
    public $paddingClasses = 'px-4 py-8 md:px-20 md:py-20 xl:px-80';
}
