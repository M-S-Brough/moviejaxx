<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Review;
use Exception;

class ReviewFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {


        $movieId = 4;
        $movie = $manager->getRepository(Movie::class)->find($movieId);

        if (!$movie) {
            throw new Exception('Movie not found');
        }

        $review = new Review();
        $review->setTitle('A timeless classic');
        $review->setReviewText('There aren\'t many movies better than Ghostbusters. This film expertly balances
         itself right between the horror genre and the comedy genre. The chemistry of the main three characters is undeniable, 
         and when you throw in interesting side characters (such as Rick Moranis as Louis Tully), every scene is a delight 
         to watch. The story is original, the effects are impressive and the jokes are funny - in short, Ghostbusters is 
         a timeless classic.');
        $movie->setReview($review);

        $manager->persist($review);
        $manager->flush();
    }
}
