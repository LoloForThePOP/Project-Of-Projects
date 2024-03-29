<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Need;
use App\Entity\User;
use App\Entity\Place;
use App\Entity\Slide;
use App\Entity\PPBase;
use App\Entity\Message;
use App\Entity\Persorg;
use App\Entity\Category;
use App\Entity\Document;
use App\Entity\Purchase;
use App\Entity\Conversation;
use App\Entity\ContributorStructure;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class FakeDataFixtures extends Fixture
{

    protected $encoder;
    protected $slugger;

    public function __construct(UserPasswordHasherInterface $encoder, SluggerInterface $slugger)
    {
        $this->encoder = $encoder;
        $this->slugger = $slugger;
    }


    /**
     * Allow to create a person or organisation thumbnail (= name; avatar picture; email; short description ...). Persorg $type is 'person' or 'organisation' (company; ngo; etc.)
     */

    public static function hydratePersorg($persorg, $type){

        $faker = Factory::create('fr-FR');

        $companyImages=['img1.jpg', 'img2.jpg', 'img3.jpg','img4.jpg', 'img5.jpg', 'img6.jpg', 'img7.jpg', 'img8.jpg', 'img9.jpg', 'img10.jpg', 'img11.png', 'img12.png'];

        $personImages=['girl1.jpg', 'girl2.jpg', 'girl3.jpg', 'girl4.jpg', 'girl5.jpg', 'girl6.jpg', 'man1.jpg', 'man2.jpg', 'man3.jpg', 'man4.jpg', 'man5.jpg', 'man6.jpg'];

        switch ($type) {
            case 'person':
                $persorg->setName($faker->name());
                $persorg->setImage($personImages[array_rand($personImages)]);
                break;
            
            case 'organisation':
                $persorg->setName($faker->company());
                $persorg->setImage($companyImages[array_rand($companyImages)]);
                break;
            
            default:
                $persorg->setName($faker->name());
                break;
        }

        $description = $faker->boolean(50) ? $faker->paragraphs(mt_rand(1,3), true) : null;
        $email = $faker->boolean(50) ? $faker->email() : null;
        $website1 = $faker->boolean(50) ? $faker->url() : null;
        $website2 = $faker->boolean(30) ? $faker->url() : null;
        $website3 = $faker->boolean(20) ? $faker->url() : null;
        $postalMail = $faker->boolean(20) ? $faker->address() : null;
        $tel1 = $faker->boolean(50) ? $faker->e164PhoneNumber() : null;
        $tel2 = $faker->boolean(50) ? $faker->e164PhoneNumber() : null;
        
        
        $persorg->setEmail($email)
                ->setDescription($description)
                ->setWebsite1($website1)
                ->setWebsite2($website2)
                ->setWebsite3($website3)
                ->setPostalMail($postalMail)
                ->setTel1($tel1)
                ->setTel2($tel2);

        return $persorg;
    }


    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('fr_FR');

        // Project Categories Creation. Categories List : array[category position; unique name; english description; french description] 

        $categories = [

            0 =>

            [
                "software",
                "Computers, Code, Internet",
                "Informatique, Codage, Internet",

            ],

            1 =>

            [
                "science",
                "Science, Research",
                "Science, Recherche",

            ],

            2 =>

            [
                "inform",
                "Inform, Educate, Learn",
                "Informer, Éduquer, Apprendre",

            ],

            3 =>

            [
                "humane",
                "Live Together, Humanitarianism",
                "Vivre Ensemble, Humanitaire",

            ],

            4 =>

            [
                "material",
                "Make, Construct, Renovate",
                "Fabriquer, Construire, Rénover",

            ],

            5 =>

            [
                "environment",
                "Environment",
                "Environnement",

            ],

            6 =>

            [
                "history",
                "History, Heritage",
                "Histoire, Patrimoine",

            ],

            7 =>

            [
                "animals",
                "Animals",
                "Animaux",

            ],

            8 =>

            [
                "money",
                "Finance, Money",
                "Finance, Argent",

            ],

            9 =>

            [
                "food",
                "Agriculture, Food",
                "Agriculture, Nourriture",

            ],

            10 =>

            [
                "services",
                "Services, Linking",
                "Services, Mise en relation",

            ],

            11 =>

            [
                "arts",
                "Culture, Arts",
                "Culture, Arts",

            ],

            12 =>

            [
                "entertainment",
                "Entertainment, Leasure, Sports",
                "Divertissements, Loisirs, Sports",

            ],

            13 =>

            [
                "data",
                "Organize data",
                "Organiser des données",

            ],

            14 =>

            [
                "health",
                "Health",
                "Santé",

            ],

            15 =>

            [
                "ideas",
                "Ideas, Politics",
                "Idées, Politique",

            ],

            16 =>

            [
                "space",
                "Air and Space",
                "Air et Espace",

            ],

            17 =>

            [
                "crisis",
                "Crisis",
                "Crise",

            ],




        ];

        // store categories as objects, to hydrate project presentations below

        $categoriesObjects = [];

        foreach ($categories as $key => $value) {

            $category = new Category();

            $category->setPosition($key)
                ->setUniqueName($value[0])
                ->setDescriptionEn($value[1])
                ->setDescriptionFr($value[2]);

            $manager->persist($category);

            $categoriesObjects[] = $category;
        }
        // End of Project Categories Creation


        // Users Creation

        $users = []; // contains all users, we'll use this array to hydrate project presentation creators

        // One admin user creation
        
        $admin = new User();

        $admin
            ->setUserName('TestUserName')
            ->setUserNameSlug(strtolower($this->slugger->slug($admin->getUserName())))
            ->setEmail('test@test.com')
            ->setPassword(
                $this->encoder->hashPassword(
                    $admin,
                    'test'
                )
            )
            ->setParameter('isVerified', true)
            ->setRoles(['ROLE_ADMIN']);


        // admin's avatar creation

        $adminPersorg = new Persorg();
        $hydratedPersorg = FakeDataFixtures::hydratePersorg($adminPersorg, 'person');
        $hydratedPersorg->setName($admin->getUserName());
        $admin->setPersorg($hydratedPersorg);

        $manager->persist($admin);

        $users[] = $admin;


        // Another test user creation

        $otherTestUser = new User();

        $otherTestUser
            ->setUserName('OtherTestUserName')
            ->setUserNameSlug(strtolower($this->slugger->slug( $otherTestUser->getUserName())))
            ->setEmail('test1@test.com')
            ->setPassword(
                $this->encoder->hashPassword(
                    $otherTestUser,
                    'test'
                )
            )
            ->setParameter('isVerified', true);

        
        // admin's avatar creation

        $otherTestUserPersorg = new Persorg();
        $hydratedPersorg = FakeDataFixtures::hydratePersorg($otherTestUserPersorg, 'person');
        $hydratedPersorg->setName($otherTestUser->getUserName());
        $otherTestUser->setPersorg($hydratedPersorg);

        $manager->persist($otherTestUser);

        $users[] = $otherTestUser; 

        // Casual users creation

        for ($i = 0; $i < 5; $i++) {

            $user = new User();

            $user
                ->setUsername($faker->userName())            
                ->setUserNameSlug(strtolower($this->slugger->slug($user->getUserName())))            
                ->setEmail($faker->email())
                ->setPassword($this->encoder->hashPassword(
                    $user,
                    'test'
                ))
                ->setParameter('isVerified', true);

            // user avatar creation

            $userPersorg = new Persorg();

            if ($faker->boolean(70)) { // some users fill their profile
                
                $userPersorg = FakeDataFixtures::hydratePersorg($userPersorg, 'person');

            }

            $userPersorg->setName($user->getUserName());
            $user->setPersorg($userPersorg);

            $manager->persist($user);

            $users[] = $user;

        }
        
        // End Of casual users creation


        // Project Presentations Creation

        

        for ($j = 0; $j < 25; $j++) {

            $presentation = new PPBase();
            // Title Creation

            $title = null;

            if ($faker->boolean(50)) {

                $title = $faker->sentence(mt_rand(1,20));
            }

            // Keywords Creation

            $keywordsNumber = mt_rand(0, 7);

            if ($keywordsNumber == 0) {
                $keywords = '';
            } else {
                $keywords = join(', ', $faker->words($keywordsNumber));
            }

            // Text Description

            $textDescription = null;

            if ($faker->boolean(85)) {

                $paragraphsCount = mt_rand(1, 5);

                $textDescription = '<p>' . join('</p><p>', $faker->paragraphs($paragraphsCount)) . '</p>';
            }

            // Project Needs Creation

            if ($faker->boolean(40)) {

                $needsCount = mt_rand(1,7);


                for ($i=0; $i < $needsCount; $i++) { 
                    
                    $need = new Need();

                    $needType= Need::TYPES[array_rand(Need::TYPES)];

                    $needIsPaid= Need::ISPAID[array_rand(Need::ISPAID)];

                    $description = $faker->boolean(70) ? $faker->paragraphs(mt_rand(1,4), true) : null;

                    $need
                    
                        ->setTitle($faker->sentence())
                        ->setDescription($description)
                        ->setIsPaid($needIsPaid)
                        ->setType($needType)
                        ->setPosition($i)
                        ->setPresentation($presentation);

                    $manager->persist($need);
                }
            }

            // Project Places Creation

            if ($faker->boolean(70)) {

                $placesCount = mt_rand(1,3);

                $placestypes = ['administrative_area_level_1','administrative_area_level_2', 'locality', 'country', 'sublocality_level_1'];

                for ($i=0; $i < $placesCount; $i++) { 
                    
                    $place = new Place();

                    $placeType = $placestypes[array_rand($placestypes)];

                    $placeName = null;
                    $postalCode = null;

                    switch ($placeType) {
                        
                        case 'administrative_area_level_1':
                            $placeName = $faker->region();
                            break;

                        case 'administrative_area_level_2':
                            $placeName = $faker->departmentName();
                            break;

                        case 'locality':
                            $placeName = $faker->city();
                            $postalCode = $faker->postCode();
                            break;

                        case 'sublocality_level_1':
                            $placeName = $faker->streetName();
                            break;

                        case 'country':
                            $placeName = $faker->country();
                            break;
                        
                        default:
                            $placeName='Default';
                            break;
                    }

                    //GeoLocalisation
                    $geoloc=[
                        
                        "lat" => floatval($faker->latitude()),
                        "lng" => floatval($faker->longitude()),
                    
                    ];
                    

                    $place->setName($placeName)
                         ->setType($placeType)
                         ->setPostalCode($postalCode)
                         ->setGeoloc($geoloc)
                         ->setPosition($i)
                         ->setPresentation($presentation);

                    $manager->persist($place);
                }
            }

            // Project Images & Videos Slideshow

            if ($faker->boolean(70)) {

                $slidesCount = mt_rand(1,8);

                $slidesTypes = ['image','youtube_video'];
                
                $imageAddresses = ['width.jpg','height.jpeg','width2.jpg','width3.png','height2.jpeg','square.webp','landscape.jpg', 'bird.jpg', 'tiger.jpg', 'scheme1.jpg', 'scheme2.jpg', 'scheme3.png', 'scheme4.jpg', 'team1.jpg', 'team2.jpg', 'team3.png'];

                $videoAddresses = ['5xQDgtsvEaw','TJawNbIGYbo','XpChfjSUFMo','fyqW_GGiqjc','zgMmC-NOhV4','sX1Y2JMK6g8','oPVte6aMprI'];

                for ($i=0; $i < $slidesCount; $i++) { 
                    
                    $slide = new Slide();

                    $slideType = $slidesTypes[array_rand($slidesTypes)];

                    switch ($slideType) {

                        case 'image':
                            $address= $imageAddresses[array_rand($imageAddresses)];
                            break;

                        case 'youtube_video':
                            $address= $videoAddresses[array_rand($videoAddresses)];
                            break;
                            
                        default:
                            break;
                    }

                    $caption = null;

                    if ($faker->boolean(65)) {
                        $caption = $faker->sentences(mt_rand(1,2), true);
                    }

                    $licenceExamples = ["Attribution 4.0 International", "CC BY-NC-SA 4.0", "BY-NC-ND 4.0 license", "Credit Reuters", "Credits : AFP", "Credit : Wikipédia", "Copyright : Wikipedia"];

                    $licence = $faker->boolean(25) ? $licenceExamples[array_rand($licenceExamples)] : null;

                    $slide->setType($slideType)
                         ->setAddress($address)
                         ->setLicence($licence)
                         ->setCaption($caption)
                         ->setPosition($i)
                         ->setPresentation($presentation);

                    $manager->persist($slide);
                }
            }

            // Project Sponsors; Partners; Acknowledgments; etc

            if ($faker->boolean(50)) {

                $structuresCount = mt_rand(1,3);

                for ($i=0; $i < $structuresCount; $i++) { 
                    
                    $structure = new ContributorStructure();

                    $title = $faker->sentence();

                    $text = $faker->boolean(50) ? $faker->paragraph(mt_rand(1,3)) : null ;

                    // set potential thumbnails of persons or organisations

                    if ($faker->boolean(70)) {

                        $persorgsCount = mt_rand(1,10);

                        for ($t=0; $t < $persorgsCount; $t++) {

                            $persorg = new Persorg();

                            $hydratedPersorg = FakeDataFixtures::hydratePersorg($persorg, 'organisation');

                            $hydratedPersorg->setPosition($t);

                            $structure->addPersorg($hydratedPersorg);

                            $manager->persist($persorg);      

                        }

                    }

                    $structure
                            ->setType('external')
                            ->setTitle($title)
                            ->setRichTextContent($text)
                            ->setPosition($i)
                            ->setPresentation($presentation);

                    $manager->persist($structure);
                }
            }

            // Attached Documents
            
            if ($faker->boolean(50)) {

                $documentsCount = mt_rand(1,8);

                $documentFileNames = ['pdf.pdf', 'docx.docx', 'xlsx.xlsx','epub.epub', 'odt.odt', 'pptx.pptx', 'rtf.rtf'];
                
                for ($i=0; $i < $documentsCount; $i++) { 
                    
                    $document = new Document();

                    $documentFileName = $documentFileNames[array_rand($documentFileNames)];

                    $documentTitle= $faker->words(mt_rand(2,18), true);

                    $document
                            ->setTitle($documentTitle)
                            ->setFileName($documentFileName)
                            ->setPosition($i)
                            ->setPresentation($presentation);

                    $manager->persist($document);
                }
            }
            

            // Websites creation 

            $numWebsites = mt_rand(0, 6);

            if ($numWebsites > 0) {

                for ($l=0; $l < $numWebsites; $l++) { 

                    $website = [];

                    $website['id'] = uniqid();
                    $website['position'] = $l;
                    $website['url'] = $faker->url();
                    $website['description'] = $faker->boolean(85) ? $faker->sentence() : null;
                    
                    $presentation->addOtherComponentItem('websites', $website);

                }

            }

            // Q&A creation 

            $numQA = mt_rand(0, 6);

            if ($numQA > 0) {

                for ($l=0; $l < $numQA; $l++) { 

                    $qa = [];

                    $qa['id'] = uniqid();
                    $qa['position'] = $l;
                    $qa['question'] = substr($faker->sentence(), 0, -1) .' ?';
                    $qa['answer'] = $faker->paragraph();

                    $presentation->addOtherComponentItem('questionsAnswers', $qa);

                }

            }

            // Miscelaneous Data List creation 

            $numList = mt_rand(0, 1);

            if ($numList > 0) {

                $numItems = mt_rand(1, 8);

                for ($l=0; $l < $numItems; $l++) { 

                    $item = [];

                    $item['id'] = uniqid();
                    $item['position'] = $l;
                    $item['name'] = substr($faker->sentence(mt_rand(1,2)), 0, -1);
                    $item['value'] = $faker->sentence(mt_rand(1,5));

                    $presentation->addOtherComponentItem('dataList', $item);

                }

            }

            // Business Cards Creation creation 

            $numBC = mt_rand(0, 4);

            if ($numBC > 0) {

                for ($l=0; $l < $numBC; $l++) { 

                    $bc = [];

                    $bc['id'] = uniqid();
                    $bc['position'] = $l;

                    $bc['title'] = $faker->name();

                    $bc['email1'] = $faker->boolean(25) ? $faker->email() : null;
                    $bc['website1'] = $faker->boolean(25) ? $faker->url() : null;
                    $bc['website2'] = $faker->boolean(15) ? $faker->url() : null;
                    $bc['tel1'] = $faker->boolean(25) ? $faker->phoneNumber() : null;
                    $bc['postalMail'] = $faker->boolean(15) ? $faker->address() : null;
                    $bc['remarks'] = $faker->boolean(15) ? $faker->sentence() : null;
                    

                    $presentation->addOtherComponentItem('businessCards', $bc);

                }

            }

            // Project Logo

            $logo = null;

            $logoChoices=['logo1.png', 'logo2.png', 'logo3.png', 'logo4.png', 'logo5.png', 'logo6.png', 'logo7.png', 'logo8.png', 'logo9.png', 'logo10.png'];

            if ($faker->boolean(50)) {
                $logo = $logoChoices[array_rand($logoChoices)];
            }

            
            // Private Messages Activation

            $privateMessagesActivation = false;

            if ($faker->boolean(90)) {

                $privateMessagesActivation = true;
            }

            // Hydrate Presentation

            $presentation->setCreator($users[array_rand($users)])
                ->setGoal($faker->sentence())
                ->setTitle($title)
                ->setLogo($logo)
                ->setTextDescription($textDescription)
                ->setKeywords($keywords)
                ->setParameter('arePrivateMessagesActivated', $privateMessagesActivation)
                ->setCreatedAt($faker->dateTimeThisDecade());


            // Project Categories (= Add some categories to this Project Presentation)

            // number of categories for this project
            $numCat = mt_rand(0, 6);

            if ($numCat > 0) {

                //select some categories at random

                $catRandKeys = array_rand($categoriesObjects, $numCat);

                if ($numCat == 1) {

                    $presentation->addCategory($categoriesObjects[$numCat]);
                } else {

                    foreach ($catRandKeys as $index) {

                        $presentation->addCategory($categoriesObjects[$index]);
                    }
                }
            }

            
            // Conversations Creation About This Presentation (private messages)

            if($privateMessagesActivation == true) {

                if ($faker->boolean(70)) {

                    for ($i=0; $i < mt_rand(1,12); $i++) { 
                        
                        $conversation = new Conversation();

                        // conversation users

                        $user1 = $presentation->getCreator();
                        $user2 = $users[array_rand($users)];

                        while ($user2 == $user1) {
                            $user2 = $users[array_rand($users)];
                        }

                        $conversation->addUser($user1);
                        $conversation->addUser($user2);

                        $user2->addConversation($conversation);
                        $user2->addConversation($conversation);

                        $conversation
                                    ->setContext($faker->sentence(mt_rand(7,20)))
                                    ->setAuthorUser($user2)
                                    ->setPresentation($presentation);

                        $messagesCount = mt_rand(1,12);

                        $conversationUsers=[$user1, $user2];

                        for ($i=0; $i <= $messagesCount; $i++) { 
                            
                            $message = new Message();

                            $author = $conversationUsers[array_rand($conversationUsers)];

                            $content = $faker->boolean(50) ? $faker->paragraph(mt_rand(1,2)) : $faker->paragraphs(mt_rand(1,3), true);

                            $message->setAuthorUser($author)
                                    ->setContent($content)
                                    ->setConversation($conversation)
                                    ->setType("between_users")
                                    ->setIsConsulted(true);

                            $conversation->setCacheItem("lastMessIsConsulted", true);

                            // sometimes last message is not consulted
                                    
                            if ($i == $messagesCount && $faker->boolean(80)) {

                                $message->setIsConsulted(false);
                                $conversation->setCacheItem("lastMessIsConsulted", false);

                                    // update receiver unread messages count

                                    foreach ($conversation->getUsers() as $participant) {
                                        if ($message->getAuthorUser() != $participant) {
    
                                            $unreadMessagesCount= $participant->getDataItem("unreadMessagesCount");
    
                                            $participant->setDataItem("unreadMessagesCount", $unreadMessagesCount+1);
                                        }
                                    }

                            }

                            $manager->persist($message);

                            $conversation->addMessage($message);

                        }

                        $manager->persist($conversation);

                    }

                }
                
            }

            $manager->persist($presentation);

        }

        // End of Project Presentation Creation

        // update users unread messages count

    /*     foreach ($users as $user) {

            $unreadMessagesCount=0;
            
            foreach ($user->getConversations() as $conversation) {

                foreach ($conversation->getMessages() as $message) {
                    
                    if ($message->getAuthorUser() != $user && $message->getIsConsulted()==false) {
                        
                        $unreadMessagesCount ++;
                    }
                }
            }

            $user->setDataItem("unreadMessagesCount", $unreadMessagesCount);
        }
 */

        // Some Purchases

        for($p=0; $p<mt_rand(20,40); $p++) {

            $purchase = new Purchase();

            $buyerEmail = $faker->email();

            $buyerInfo=[];
            $buyerInfo['telephone'] = $faker->phoneNumber();

            // Hydrating Purchase :

            if ($faker->boolean(90)) {
                $purchase->setStatus("PAID");
            }
            $purchase->setRegistredUser($faker->randomElement($users));
            $purchase->setBuyerEmail($buyerEmail);
            $purchase->setBuyerInfo($buyerInfo);

            $manager->persist($purchase);
            

        };


        $manager->flush();

    }
}
