<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;


class CreatePacksTable extends Migration
{
    public function up()
    {
        DB::beginTransaction();
        
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Schema::dropIfExists('packs');
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            Schema::create('packs', function ($table) {
                $table->id();
                $table->string('nom');
                $table->float('prix');
                $table->timestamp('durée');
                $table->text('description');
                $table->text('image');
                $table->timestamps();
            });
            
            $packs = [
                [
                    'nom' => 'Pack Gold',
                    'prix' => 100,
                    'durée' => now()->addMonths(12),
                    'description' => 'pack gold duree a 1 an ',
                    'image' => $this->getEncodedImage('public/images/1.png'), // Store image as BLOB
                ],
                [
                    'nom' => 'Pack Silver',
                    'prix' => 75,
                    'durée' => now()->addMonths(9),
                    'description' => 'pack silver duree a 6 mois, profiter ',
                    'image' => $this->getEncodedImage('public/images/1.png'), // Store image as BLOB
                ],
                [
                    'nom' => 'Pack Bronze',
                    'prix' => 50,
                    'durée' => now()->addMonths(6),
                    'description' => 'pack bronze duree a 6 moid duree d\'utilisation ',
                    'image' => $this->getEncodedImage('public/images/1.png'), // Store image as BLOB
                ],
            ];
            
            DB::table('packs')->insert($packs);
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    
    protected function getEncodedImage($path)
    {
        // Load the image
        $image = imagecreatefrompng(Storage::path($path));
    
        // Resize the image to 500px width and proportional height
        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);
        $newWidth = 500;
        $newHeight = intval(($originalHeight / $originalWidth) * $newWidth);
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
    
        // Encode the image as JPEG with 90% quality and return the base64-encoded string
        ob_start();
        imagejpeg($resizedImage, null, 90);
        $encodedImage = base64_encode(ob_get_clean());
    
        return $encodedImage;
    }
    

    public function down()
    {
        Schema::dropIfExists('packs');
    }
}
