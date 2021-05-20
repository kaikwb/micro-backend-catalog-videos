<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Respect\Validation\Validator as validator;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Genre::class)->create();
        $Genres = Genre::all();
        $this->assertCount(1, $Genres);
        $GenreKey = array_keys($Genres->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at'
            ],
            $GenreKey
        );
    }

    public function testCreate()
    {
        $Genre = Genre::create([
            'name' => 'test1'
        ]);
        $Genre->refresh();

        $this->assertTrue(validator::uuid()->validate($Genre->id));
        $this->assertEquals('test1', $Genre->name);
        $this->assertTrue($Genre->is_active);

        $Genre = Genre::create([
            'name' => 'test1',
            'is_active' => false
        ]);

        $this->assertFalse($Genre->is_active);

        $Genre = Genre::create([
            'name' => 'test1',
            'is_active' => true
        ]);

        $this->assertTrue($Genre->is_active);
    }

    public function testUpdate()
    {
        $Genre = factory(Genre::class)->create([
            'is_active' => false
        ]);

        $data = [
            'name' => 'test_name_updated',
            'is_active' => true
        ];

        $Genre->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $Genre->{$key});
        }
    }

    public function testDelete()
    {
        $Genre = factory(Genre::class)->create();
        $Genre->delete();
        $this->assertNull(Genre::find($Genre->id));
    }
}
