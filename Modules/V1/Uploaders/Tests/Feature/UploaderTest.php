<?php

namespace Modules\V1\Uploaders\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploaderTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUploadFile(): void
    {
        $file = UploadedFile::fake()->create('fake.txt');

        $response = $this->call('POST', '/api/v1/files', ['file' => $file]);
        $this->assertEquals(200, $response->status());

        // Delete File :)
        $createdFilePath = 'files/'.$response['data']['data']['filename'];
        Storage::delete($createdFilePath);
        $this->assertFalse(Storage::exists($createdFilePath));
    }
}
