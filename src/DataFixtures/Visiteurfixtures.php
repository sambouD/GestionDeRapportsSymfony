<?php

namespace App\DataFixtures;

use App\Entity\ActiviteComplet;
use App\Entity\Inviter;
use App\Entity\Medicament;
use App\Entity\Offrir;
use Faker\Factory;
use App\Entity\Praticien;
use App\Entity\RapportVisite;
use App\Entity\Role;
use App\Entity\Visiteur;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\User;

class Visiteurfixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');

        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new Visiteur();
        $adminUser->setNom('Mantellato')
                  ->setPrenom('Xavier')
                  ->setLogin('xmantellato')
                  ->setMdp($this->encoder->encodePassword($adminUser, 'Password'))
                  ->setAdresse('11 impasse de la ferme')
                  ->setCp('78340')
                  ->setVille('Les Clayes Sous Bois')
                  ->setDateEmbouche($faker->dateTimeBetween('-20 years' , 'now'))
                  ->addUserRole($adminRole);
        $manager->persist($adminUser);


    




        //ActiviteComplet
        $activiteComplets = [];// Foreign Key

        for ($i=0; $i <=30 ; $i++) { 
           
            $activiteComplet = new ActiviteComplet();

            $acDate = $faker->dateTimeBetween('now', '+30 years');
            $acTheme = $faker->sentence();
            $acLieu = $faker->city();

            $activiteComplet->setAcDate($acDate)
                            ->setAcLieu($acLieu)
                            ->setAcTheme($acTheme);
        
            $manager->persist($activiteComplet);
            $activiteComplets[] = $activiteComplet; // Foreign Key
        }

        //Praticien
        $genres = ["male" , "female"];
        $praticiens =[];// Foreign Key

        for ($i=0; $i <= 30 ; $i++) { 
            # code...
            $praticien = new Praticien();
            

            $praticien->setNom($faker->lastName())
                      ->setPrenom($faker->lastName($genres[mt_rand(0,1)]));
            
            $manager->persist($praticien);
            $praticiens[] = $praticien; // Foreign Key
        }

        //Visiteur
        
        $visiteurs=[];// Foreign Key

        for ($i=0; $i <= 30; $i++) { 
            $visiteur = new Visiteur();
       
            $login = $faker->userName();
            $mdp = $faker->password();
            $adresse = $faker->address();
            $cp = $faker->postcode();
            $ville = $faker->city(); 
            $dateEmbauche = $faker->dateTimeBetween('-20 years' , 'now');        
            
            $hash = $this->encoder->encodePassword($visiteur, 'Password');

            $visiteur->setNom($faker->lastName())
                     ->setPrenom($faker->lastName($genres[mt_rand(0,1)]))
                     ->setLogin($login)
                     ->setMdp($hash)
                     ->setAdresse($adresse)
                     ->setCp($cp)
                     ->setVille($ville)
                     ->setDateEmbouche($dateEmbauche);
        
            $manager->persist($visiteur);  
            $visiteurs[] = $visiteur; // Foreign Key
        }

    //Medicament
        $med = []; // Foreign Key

        $medicaments=fopen(__DIR__."/medicaments.csv","r");
        while (!feof($medicaments)) {
          $lesmedicaments[]=fgetcsv($medicaments);
        }
        fclose($medicaments);

    foreach ($lesmedicaments as $value) {
      $medicament = new Medicament();
      $medicament->setId(intval($value[0]))
                ->setLibelle($value[1]);
        
        $manager->persist($medicament); 
        
        $med[] = $medicament; // Foreign Key
    }

    //rapport_visite
    $rapport_visites = []; // Foreign Key
    $rap_motif = ["Périodicité", "Actualisation", "Relance", "Sollicitation praticien", "Autre"];
    for ($i=0; $i <=30; $i++) { 
        $rapport_visite = new RapportVisite();

        $rap_date = $faker->dateTimeBetween('-10 years' , 'now');  
     
        $rap_bilan = $faker->sentence(3, true);

        $rapport_visite->setVisiteur($visiteurs[mt_rand(0,30)])
                        ->setPraticien($praticiens[mt_rand(0,30)])
                        ->setRapDate($rap_date)
                        ->setRapBilan($rap_bilan)
                        ->setRapMotif($rap_motif[mt_rand(0,4)]);

        $manager->persist($rapport_visite); 
        $rapport_visites[] = $rapport_visite; // Foreign Key
    }

    //Inviter
    for ($i=0; $i <= 30 ; $i++) { 
        $inviter = new Inviter();
        
        $spe = $faker->word();

        $inviter->setPraticien($praticiens[mt_rand(0,30)])
                ->setActiviteCompl($activiteComplets[mt_rand(0,30)])
                ->setSpecialisation($spe);

        $manager->persist($inviter);
    }

    //Offrir 

    for ($i=0; $i <= 20; $i++) { 
        
        $offrir = new Offrir();

        //Faker 
        $qte = $faker->numberBetween(1 , 100);

        $offrir->setMedicament($med[mt_rand(0,13)])
               ->setRapportVisite($rapport_visites[mt_rand(0,30)])
               ->setOffQte($qte);
    
        $manager->persist($offrir);
    }



        $manager->flush();
    }
}
