<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\City;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:seed', description: 'Create the initial categories and the Deventer area cities')]
class SeedCommand extends Command
{
    private const CATEGORIES = [
        'books' => ['Boeken', 'Books', 'كتب'],
        'music' => ['Muziek', 'Music', 'موسيقى'],
        'devices' => ['Apparaten', 'Devices', 'أجهزة'],
        'cameras' => ['Camera\'s', 'Cameras', 'كاميرات'],
        'clothes' => ['Kleding', 'Clothes', 'ملابس'],
    ];

    private const CITIES = ['Deventer', 'Colmschate', 'Diepenveen', 'Schalkhaar', 'Bathmen', 'Lettele', 'Okkenbroek', 'Epse'];

    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach (self::CATEGORIES as $slug => [$nl, $en, $ar]) {
            if (!$this->em->getRepository(Category::class)->findOneBy(['slug' => $slug])) {
                $this->em->persist((new Category())->setSlug($slug)->setNameNl($nl)->setNameEn($en)->setNameAr($ar));
            }
        }
        foreach (self::CITIES as $name) {
            if (!$this->em->getRepository(City::class)->findOneBy(['name' => $name])) {
                $this->em->persist((new City())->setName($name)->setRegion('Deventer'));
            }
        }
        $this->em->flush();
        $output->writeln('Seeded.');

        return Command::SUCCESS;
    }
}
