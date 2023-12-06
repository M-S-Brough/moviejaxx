<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MovieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $movie = new Movie();
        $movie->setDirector('Robert Zemeckis');
        $movie->setRunningTime(116);
        $movie->setTitle('Back to the Future');
        $movie->setReleaseYear(1985);
        $movie->setCast(['Michael J. Fox', 'Christopher Lloyd']);
        $movie->setImage('build/images/back_to_the_future.bf3a5b97.jpg');
        $manager->persist($movie);

        $movie2 = new Movie();
        $movie2->setDirector('The Russo Brothers');
        $movie2->setRunningTime(181);
        $movie2->setTitle('Avengers: Endgame');
        $movie2->setReleaseYear(2019);
        $movie2->setCast(['Robert Downey Jr.', 'Chris Evans']);
        $movie2->setImage('build/images/avengers_endgame.4c12d6d4.jpg');
        $manager->persist($movie2);

        $movie3 = new Movie();
        $movie3->setDirector('Christopher Nolan');
        $movie3->setRunningTime(152);
        $movie3->setTitle('The Dark Knight');
        $movie3->setReleaseYear(2008);
        $movie3->setCast(['Christian Bale', 'Heath Leader']);
        $movie3->setImage('build/images/the_dark_knight.0206fced.jpg');
        $manager->persist($movie3);

        $movie4 = new Movie();
        $movie4->setDirector('Ivan Reitman');
        $movie4->setRunningTime(145);
        $movie4->setTitle('Ghostbusters');
        $movie4->setReleaseYear(1984);
        $movie4->setCast(['Dan Aykroyd', 'Bill Murray', 'Harold Ramis']);
        $movie4->setImage('build/images/ghostbusters.3b686ebf.jpg');
        $manager->persist($movie4);

        $manager->flush();
    }
}