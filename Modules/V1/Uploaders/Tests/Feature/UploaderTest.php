<?php

namespace Modules\V1\Uploaders\Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UploaderTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_upload_file(): void
    {
        $file = UploadedFile::fake()->create('fake.txt');

        $response = $this->call('POST', '/api/v1/files', ['file' => $file]);
        $this->assertEquals(200, $response->status());

        // Delete File :)
        $createdFilePath = 'files/' . $response['data']['data']['filename'];
        Storage::delete($createdFilePath);
        $this->assertFalse(Storage::exists($createdFilePath));
    }
}
