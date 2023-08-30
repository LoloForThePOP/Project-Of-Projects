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
                    
                    ['uniqueName'=> 'idea', 'description_fr'=>"Encore à l'état d'idée,<br>de réflexion"], 
            
                    ['uniqueName'=> 'production', 'description_fr'=>'La réalisation concrète<br>est démarrée'] ,

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

                'description_fr'=> "S'il y a ou aura des ventes", 
    
            ],


            'items'=> [

                [ 
                    'uniqueName'=> "will_be_marketed",
                    'description_fr' => "Le produit ou service<br>n'est pas encore commercialisé",
                ], 
        
                [ 
                    'uniqueName'=> "sale_offering_began",
                    'description_fr' => "Le produit ou service<br>commence à être commercialisé",
                ], 
        
                [ 
                    'uniqueName'=> "first_sales_done",
                    'description_fr' => "Des premières ventes sont effectuées",
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

                'description_fr'=> 'Si vous créez<br>un objet matériel', 

            ],


            'items'=> [

                [ 
                    'uniqueName'=> "computer_simulation",
                    'description_fr' => "C'est actuellement une<br>simulation informatique",
                ], 
        
                [ 
                    'uniqueName'=> "labo_prototype",
                    'description_fr' => "C'est actuellement un<br>prototype testé en labo",
                ], 
      
                [ 
                    'uniqueName'=> "real_world_prototype",
                    'description_fr' => "C'est actuellement un prototype<br>testé dans le monde réel",
                ], 
      
                [ 
                    'uniqueName'=> "realised_object",
                    'description_fr' => "L'objet est réalisé",
                ], 
        
            ],

        ], 

        [
            'categoryLabels' =>

            [

                'uniqueName' => 'submission',

                'description_fr'=> "Si le projet est soumis<br>à une décision ou un vote", 

            ],


            'items'=> [

                [ 
                    'uniqueName'=> "submitted",
                    'description_fr' => "Proposé, soumis<br>(en attente de décision)",
                ], 
        
                [ 
                    'uniqueName'=> "approved",
                    'description_fr' => "Décision acceptée",
                ], 
        
                [ 
                    'uniqueName'=> "rejected",
                    'description_fr' => "Décision rejetée",
                ], 
        
                [ 
                    'uniqueName'=> "postponed",
                    'description_fr' => "Décision reportée",
                ], 
        
            ],

        ], 
        

    ];
    

}