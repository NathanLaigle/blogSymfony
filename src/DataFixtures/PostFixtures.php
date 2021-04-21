<?php

namespace App\DataFixtures;

use App\Entity\Post;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class PostFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i = 0; $i<=10; $i++){
            $post = new Post;
            $post->setTitle('Titre')
                ->setContent('Je suis un content')
                ->setImage('url')
                ->setCreatedAt(new \DateTime());
            $manager->persist($post);
        }
        $manager->flush();
    }
}
