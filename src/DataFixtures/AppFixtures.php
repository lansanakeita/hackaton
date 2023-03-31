<?php

namespace App\DataFixtures;

use App\Entity\Equipments;
use App\Entity\Kitchens;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $kitchens = array(
            "Machine à café",
            "Bouilloire électrique",
            "Four à micro-ondes",
            "Réfrigérateur",
            "Lave-vaisselle",
            "Distributeur d'eau",
            "Grille-pain",
            "Blender",
            "Cuiseur à riz",
            "Plaque chauffante électrique",
            "Batteur électrique",
            "Presse-agrumes électrique"
        );
        foreach ($kitchens as $nameAccess) {
            $kitchenAccess = new Kitchens();
            $kitchenAccess->setName($nameAccess);
            $manager->persist($kitchenAccess);
        }

        $equipments = array(
            "Bureau et chaise de travail",
            "Ecran",
            "Clavier",
            "Souris",
            "Téléphone",
            "Imprimante",
            "Scanner",
            "Projecteur",
            "Tableau blanc",
            "Climatisation",
            "Chauffage",
            "Éclairage",
            "Salle de réunion",
            "Espace de rangement",
        );

        foreach ($equipments as $nameEquipment) {
            $equipment = new Equipments();
            $equipment->setName($nameEquipment);
            $manager->persist($equipment);
        }

        $manager->flush();
    }
}
