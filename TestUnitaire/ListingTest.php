<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/bootstrap.php';

use PHPUnit\Framework\TestCase;
use App\Models\Listing;

final class ListingTest extends TestCase {

    public function testFindExistingListing(): void {

        $all = Listing::all();
        if (empty($all)) {
            $this->markTestSkipped('Aucune annonce en base de données pour effectuer le test.');
        }
        
        $id = $all[0]['id']; 
        $listing = Listing::find($id);

        $this->assertIsArray($listing, "L'annonce avec l'ID $id devrait exister");
        
        $this->assertEquals($id, $listing['id']);
        
        $this->assertArrayHasKey('title', $listing);
    }

    public function testFindNonExistingListing(): void {

        $id = 999999;
        $listing = Listing::find($id);

        $this->assertNull($listing, "L'annonce avec l'ID $id ne devrait pas exister");
    }
}
