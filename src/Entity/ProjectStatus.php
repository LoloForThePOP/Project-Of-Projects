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
                    
                    ['uniqueName'=> 'idea', 'description_fr'=>"À l'étude (idée, réflexion)"], 
            
                    ['uniqueName'=> 'production', 'description_fr'=>'Réalisation démarrée'] ,

                    [
                        'uniqueName'=> "pause", 'description_fr' => "En pause"
                    ], 
            
                    ['uniqueName'=> "cancel",'description_fr' => "Annulé ou abandonné"], 
            
                    ['uniqueName'=> "done",'description_fr' => "Terminé"], 
                    
                ]
            
        ], 
          
        
        [
            
            'categoryLabels' =>

            [
                'uniqueName' => 'sales',

                'description_fr'=> 'Ventes (ou pas)', 
    
            ],


            'items'=> [

                [ 
                    'uniqueName'=> "will_be_marketed",
                    'description_fr' => "Sera commercialisé",
                ], 
        
                [ 
                    'uniqueName'=> "sale_offering_began",
                    'description_fr' => "Commence à être commercialisé",
                ], 
        
                [ 
                    'uniqueName'=> "first_sales_done",
                    'description_fr' => "Premières ventes effectuées",
                ], 
        
                [ 
                    'uniqueName'=> "marketed_significantly",
                    'description_fr' => "Déjà commercialisé à moyenne ou grande échelle",
                ], 
        
            ],

        ], 
        
        
        [

            'categoryLabels' =>

            [

                'uniqueName' => 'modelisation',

                'description_fr'=> 'Objet matériel', 

            ],


            'items'=> [

                [ 
                    'uniqueName'=> "computer_simulation",
                    'description_fr' => "Simulation informatique",
                ], 
        
                [ 
                    'uniqueName'=> "labo_prototype",
                    'description_fr' => "Prototype testé en labo",
                ], 
      
                [ 
                    'uniqueName'=> "real_world_prototype",
                    'description_fr' => "Prototype testé en monde réel",
                ], 
      
                [ 
                    'uniqueName'=> "realised_object",
                    'description_fr' => "Objet réalisé",
                ], 
        
            ],

        ], 

        [
            'categoryLabels' =>

            [

                'uniqueName' => 'submission',

                'description_fr'=> 'Soumission ou vote', 

            ],


            'items'=> [

                [ 
                    'uniqueName'=> "submitted",
                    'description_fr' => "Proposé, soumis<br>(en attente de décision)",
                ], 
        
                [ 
                    'uniqueName'=> "approved",
                    'description_fr' => "Décision : Accepté",
                ], 
        
                [ 
                    'uniqueName'=> "rejected",
                    'description_fr' => "Décision : Rejeté",
                ], 
        
                [ 
                    'uniqueName'=> "postponed",
                    'description_fr' => "Décision Reportée",
                ], 
        
            ],

        ], 
        

    ];
    

}