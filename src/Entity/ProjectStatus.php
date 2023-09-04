<?php

namespace App\Entity;

class ProjectStatus
{
    public const CATALOG = [ 

        // Status are sorted by categories
        // Here is the first category (it has a unique name, along with a readable description in different languages, and childs within this category).
       
        [
            'categoryLabels' =>

                [
                    'uniqueName'=> 'general', 

                    'description_fr'=> 'Votre projet est',                    
                ],
            
            'items'=>  
            
                [
                    
                    [
                        'uniqueName'=> 'idea', 
                        'description_fr'=> "Encore à l'état d'idée, de réflexion / prévu",
                        'short_description_fr'=> "Idée / prévu",
                        'bg_color'=> "#95d2ff4a",
                    ], 
        
                    [
                        'uniqueName'=> 'production',
                        'description_fr'=>'La réalisation concrète est démarrée',
                        'short_description_fr'=> "En cours",
                        'bg_color'=> "#e7fbbb",
                    ],

                    [
                        'uniqueName'=> "pause", 
                        'description_fr' => "En pause",
                        'short_description_fr' => "En pause",
                        'bg_color' => "#fffd8c",
                    ], 
            
                    [
                        'uniqueName'=> "cancel",
                        'description_fr' => "Annulé ou abandonné",
                        'short_description_fr' => "Annulé / abandonné",
                        'bg_color' => "#f9dad2e0",

                    ], 
            
                    [
                        'uniqueName'=> "done",
                        'description_fr' => "Terminé",
                        'short_description_fr' => "Terminé ✓",
                        'bg_color' => "#e2ffb7",

                    ], 
                    
                ]
            
        ], 
          
        
        [
            
            'categoryLabels' =>

            [
                'uniqueName' => 'sales',
                'description_fr'=> "S'il y a ou aura des ventes", 
    
            ],


            'items'=> [

                [ 
                    'uniqueName'=> "will_be_marketed",
                    'description_fr' => "Le produit ou service n'est pas encore commercialisé",
                    'short_description_fr' => "Sera commercialisé",
                    'bg_color' => "#ffb4717d",

                ], 
        
                [ 
                    'uniqueName'=> "product_for_sale",
                    'description_fr' => "Le produit ou service commence à être commercialisé",
                    'short_description_fr' => "Commercialisé",
                    'bg_color' => "#ffb4717d",

                ], 
        
                [ 
                    'uniqueName'=> "first_sales_done",
                    'description_fr' => "Des premières ventes sont effectuées",
                    'short_description_fr' => "Premières ventes effectuées",
                    'bg_color' => "#ffb4717d",

                ], 
        
                [ 
                    'uniqueName'=> "marketed_significantly",
                    'description_fr' => "Déjà commercialisé à moyenne ou grande échelle",
                    'short_description_fr' => "Commercialisé à moyenne / grande échelle",
                    'bg_color' => "#ffb4717d",

                ], 
        
            ],

        ], 
        
        
        [

            'categoryLabels' =>

            [

                'uniqueName' => 'modelisation',
                'description_fr'=> 'Si vous créez un objet matériel', 

            ],


            'items'=> [

                [ 
                    'uniqueName'=> "computer_simulation",
                    'description_fr' => "C'est actuellement une simulation informatique",
                    'short_description_fr' => "Actuellement simulation informatique",
                    'bg_color' => "#d18eff4a",

                ], 
        
                [ 
                    'uniqueName'=> "labo_prototype",
                    'description_fr' => "C'est actuellement un prototype testé en labo",
                    'short_description_fr' => "Actuellement prototype en labo",
                    'bg_color' => "#d18eff4a",

                ], 
      
                [ 
                    'uniqueName'=> "real_world_prototype",
                    'description_fr' => "C'est actuellement un prototype testé en conditions réelles",
                    'short_description_fr' => "Actuellement prototype en conditions réelles",
                    'bg_color' => "#d18eff4a",

                ], 
      
                [ 
                    'uniqueName'=> "realised_object",
                    'description_fr' => "L'objet est réalisé",
                    'short_description_fr' => "Objet réalisé",
                    'bg_color' => "#d18eff4a",

                ], 
        
            ],

        ], 

        [
            'categoryLabels' =>

            [

                'uniqueName' => 'submission',
                'description_fr'=> "Si le projet est soumis à une décision ou un vote", 

            ],


            'items'=> [

                [ 
                    'uniqueName'=> "submitted",
                    'description_fr' => "Le projet est proposé (décision en attente)",
                    'short_description_fr' => "Décision en attente",
                    'bg_color' => "pink",

                ], 
        
                [ 
                    'uniqueName'=> "approved",
                    'description_fr' => "Décision acceptée",
                    'short_description_fr' => "Décision acceptée",
                    'bg_color' => "pink",

                ], 
        
                [ 
                    'uniqueName'=> "rejected",
                    'description_fr' => "Décision rejetée",
                    'short_description_fr' => "Décision rejetée",
                    'bg_color' => "pink",

                ], 
        
                [ 
                    'uniqueName'=> "postponed",
                    'description_fr' => "La décision est reportée à une date indéterminée",
                    'short_description_fr' => "Décision reportée (date indéterminée)",
                    'bg_color' => "pink",

                ], 
        
            ],

        ], 
        

    ];
    

}