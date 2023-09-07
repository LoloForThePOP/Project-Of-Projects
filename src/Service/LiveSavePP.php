<?php

namespace App\Service;

use App\Entity\PPBase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class LiveSavePP {


    protected $entityName;
    protected $entityId;
    protected $property;
    protected $subProperty;
    protected $subId;
    protected $content;

    protected $entity;
    protected $pp;

    protected $em;
    protected $validator;
    protected $security;




    public function __construct(Security $security, EntityManagerInterface $em, ValidatorInterface $validator)
    {
      
        $this->security = $security;
        $this->em = $em;
        $this->validator = $validator;

    }


    public function hydrate($entityName, $entityId, $property, $subId, $subProperty, $content){

        $this->entityName = $entityName;
        $this->entityId = $entityId;
        $this->property = $property;
        $this->subProperty = $subProperty;
        $this->subId = $subId;
        $this->content = $content;

        $this->setEntityToUpdate();
        $this->setPPToUpdate();

    }


  
    


    /**
     * Allow to get the entity we will update (ex: PPBase; Slide; etc)
     * @return void
     */
    public function setEntityToUpdate(){

        $entityFullName = 'App\\Entity\\'.ucfirst($this->entityName);

        return $this->entity = $this->em->getRepository($entityFullName)->findOneById($this->entityId);
        
    }

    /**
     * Allow to get the project presentation concerned by the update
     * This further allows to check if user can edit this presentation
     */
    public function setPPToUpdate(){

        $pp = null;

        if ($this->entity instanceof PPBase) {
            $pp = $this->entity;
        }else{ // we manage a PPBase child entity (ex: a slide) and we get its parent PPBase
            $pp = $this->entity->getPresentation();
        } 

        return $this->pp = $pp;

    }

    /**
     * Check if user is granted to edit the concerned project presentation
     */
    public function allowUserAccess(){

        return $this->security->isGranted('edit', $this->pp);

    }

    
    /**
    * Check if the entity, property, or subproperty can be ajax updated.
    */
    public function allowItemAccess(){
      
        $allowAccess = false;

        switch ($this->entityName) {
            case 'PPBase':
            case 'Slide':
            case 'Need':
    
                $allowAccess = true;
                break;
            
            default:
                throw new \Exception("Unsupported entity type to edit");
                break;
        }
        
        switch ($this->property) {
    
            case 'goal':
            case 'title':
            case 'textDescription':
            case 'caption':
            case 'description':
            case 'websites':
            case 'questionsAnswers':
            case 'dataList':
            case 'status':
    
                $allowAccess = true;
                break;
            
            default:
                throw new \Exception("Unsupported property to edit");
                break;
    
        }

        if (isset($this->subProperty)) {

            switch ($this->subProperty) {
    
                case 'url':
                case 'description':
                case 'question':
                case 'answer':
                case 'name':
                case 'value':
                case 'userRemarks':
                case 'general':
                case 'like':

                case 'modelisation': //project status categories
                case 'sales':
                case 'administrative':
                case 'submission':
        
                    $allowAccess = true;
                    break;
                
                default:
                    throw new \Exception("Unsupported subproperty to edit");
                    break;
        
            }
        }

        return $allowAccess;

    }

    
    /**
    * Check if content to update is valid.
    * Ex : property : websites; subproperty : url; content : www.propon.org
    */
    public function validateContent(){

        $constraints = null;
        $errors = null;

        switch ($this->property) {

            case 'websites':

                switch ($this->subProperty) {

                    case 'url':

                        $constraints = [
                            new Assert\Url(['message' => 'Vous devez utiliser une adresse web valide']),
                            new Assert\NotBlank(['message' => 'Veuillez remplir ce champ'])
                        ];

                        break;
                    
                    default:

                        return true;                        
                        break;
                }

                break;
            
            default:

                return true;

        }

        // use the validator to validate the value
        $errors = $this->validator->validate(
            $this->content,
            $constraints
        ); 
            
        if ($errors->count() > 0) {

            return  $errors[0]->getMessage();
                    
        } else {

            return true;

        }

    }

    public function save(){

        switch ($this->property) {

            case 'status':

                $this->pp->editProjectStatus($this->subProperty, $this->content);
                break;

            case 'websites': //these special cases : we update the following proporty in PPBase entity : $otherComponents
            case 'questionsAnswers':
            case 'dataList':

                $item = $this->pp->getOCItem($this->property, $this->subId); //ex: a website
                $item[$this->subProperty] = $this->content; // updating item subproperty (ex: website url)
                $this->pp->setOCItem($this->property, $this->subId, $item);

                break;
            
            default: //updating an entity property (ex: a Slide $caption)

                $propertySetterName =  'set'.ucfirst($this->property); 
                $this->entity->$propertySetterName($this->content);

                break;

        }

        $this->em->flush();

    }
    












}
