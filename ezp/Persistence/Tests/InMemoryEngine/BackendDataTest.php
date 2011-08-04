<?php
/**
 * File contains: ezp\Persistence\Tests\InMemoryEngine\BackendDataTest class
 *
 * @copyright Copyright (C) 1999-2011 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace ezp\Persistence\Tests\InMemoryEngine;
use PHPUnit_Framework_TestCase;

/**
 * Test case for Handler using in memory storage.
 *
 */
class BackendDataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Backend
     */
    protected $backend;

    /**
     * Setup the HandlerTest.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->backend = new Backend( json_decode( file_get_contents( __DIR__ . '/data.json' ), true ) );

        for ( $i = 0; $i < 10; ++$i)
            $this->backend->create(
                "Content",
                array(
                    "name" => "bar{$i}",
                    "ownerId" => 42
                )
            );

        for ( $i = 0; $i < 10; ++$i)
            $this->backend->create(
                "Content",
                array(
                    "name" => "foo{$i}",
                )
            );
    }

    /**
     * Test finding content without results
     *
     * @dataProvider providerForFindEmpty
     * @covers ezp\Persistence\Tests\InMemoryEngine\Backend::find
     */
    public function testFindEmpty( $searchData )
    {
        $this->assertEquals(
            array(),
            $this->backend->find( "Content", $searchData )
        );
    }

    public function providerForFindEmpty()
    {
        return array(
            array( array( "unexistingKey" => "bar0" ) ),
            array( array( "unexistingKey" => "bar0", "baz0" => "buzz0" ) ),
            array( array( "foo0" => "unexistingValue" ) ),
            array( array( "foo0" => "unexistingValue", "baz0" => "buzz0" ) ),
            array( array( "foo0" => "" ) ),
            array( array( "foo0" => "bar0", "baz0" => "" ) ),
            array( array( "foo0" => "bar0", "baz0" => "buzz1" ) ),
            array( array( "foo0" ) ),
            array( array( "int" ) ),
            array( array( "float" ) ),
        );
    }

    /**
     * Test finding content with results
     *
     * @dataProvider providerForFind
     * @covers ezp\Persistence\Tests\InMemoryEngine\Backend::find
     */
    public function testFind( $searchData, $result )
    {
        $list = $this->backend->find( "Content", $searchData );
        foreach ( $list as $key => $content )
        {
            $this->assertEquals( $result[$key]['id'], $content->id );
            $this->assertEquals( $result[$key]['name'], $content->name );
        }
    }

    public function providerForFind()
    {
        return array(
            array(
                array( "name" => "bar0" ),
                array(
                    array(
                        "id" => 1,
                        "name" => "bar0",
                    )
                )
            ),
            array(
                array( "name" => "foo5" ),
                array(
                    array(
                        "id" => 16,
                        "name" => "foo5",
                    )
                )
            ),
            array(
                array( "ownerId" => 42 ),
                array(
                    array(
                        "id" => 1,
                        "name" => "bar0",
                    ),
                    array(
                        "id" => 2,
                        "name" => "bar1",
                    ),
                    array(
                        "id" => 3,
                        "name" => "bar2",
                    ),
                    array(
                        "id" => 4,
                        "name" => "bar3",
                    ),
                    array(
                        "id" => 5,
                        "name" => "bar4",
                    ),
                    array(
                        "id" => 6,
                        "name" => "bar5",
                    ),
                    array(
                        "id" => 7,
                        "name" => "bar6",
                    ),
                    array(
                        "id" => 8,
                        "name" => "bar7",
                    ),
                    array(
                        "id" => 9,
                        "name" => "bar8",
                    ),
                    array(
                        "id" => 10,
                        "name" => "bar9",
                    ),
                ),
            ),
        );
    }

    /**
     * Test counting content without results
     *
     * @dataProvider providerForFindEmpty
     * @covers ezp\Persistence\Tests\InMemoryEngine\Backend::count
     */
    public function testCountEmpty( $searchData )
    {
        $this->assertEquals(
            0,
            $this->backend->count( "Content", $searchData )
        );
    }

    /**
     * Test counting content with results
     *
     * @dataProvider providerForFind
     * @covers ezp\Persistence\Tests\InMemoryEngine\Backend::count
     */
    public function testCount( $searchData, $result )
    {
        $this->assertEquals(
            count( $result ),
            $this->backend->count( "Content", $searchData )
        );
    }

    /**
     * Test loading content without results
     *
     * @dataProvider providerForLoadEmpty
     * @covers ezp\Persistence\Tests\InMemoryEngine\Backend::load
     */
    public function testLoadEmpty( $searchData )
    {
        $this->assertNull(
            $this->backend->load( "Content", $searchData )
        );
    }

    public function providerForLoadEmpty()
    {
        return array(
            array( "" ),
            array( null ),
            array( 0 ),
            array( 0.1 ),
            array( "0" ),
            array( "unexistingKey" ),
        );
    }

    /**
     * Test loading content with results
     *
     * @dataProvider providerForLoad
     * @covers ezp\Persistence\Tests\InMemoryEngine\Backend::load
     */
    public function testLoad( $searchData, $result )
    {
        //$this->markTestSkipped( "Invalid test" );
        $content = $this->backend->load( "Content", $searchData );
        foreach ( $result as $name => $value )
            $this->assertEquals( $value, $content->$name );
    }

    public function providerForLoad()
    {
        return array(
            array(
                1,
                array(
                    "id" => 1,
                    "name" => "bar0",
                    "ownerId" => 42,
                )
            ),
            array(
                "1",
                array(
                    "id" => 1,
                    "name" => "bar0",
                    "ownerId" => 42,
                )
            ),
            array(
                2,
                array(
                    "id" => 2,
                    "name" => "bar1",
                    "ownerId" => 42,
                )
            ),
            array(
                11,
                array(
                    "id" => 11,
                    "name" => "foo0",
                    "ownerId" => null,
                )
            ),
        );
    }

    /**
     * Test updating content on unexisting ID
     *
     * @covers ezp\Persistence\Tests\InMemoryEngine\Backend::update
     */
    public function testUpdateUnexistingId()
    {
        $this->assertFalse(
            $this->backend->update( "Content", 0, array() )
        );
    }

    /**
     * Test updating content with an extra attribute
     *
     * @covers ezp\Persistence\Tests\InMemoryEngine\Backend::update
     */
    public function testUpdateNewAttribute()
    {
        $this->assertTrue(
            $this->backend->update( "Content", 1, array( "ownerId" => 5 ) )
        );
        $content = $this->backend->load( "Content", 1 );
        $this->assertEquals( 1, $content->id );
        $this->assertEquals( 'bar0', $content->name );
        $this->assertEquals( 5, $content->ownerId );
    }

    /**
     * Test updating content
     *
     * @covers ezp\Persistence\Tests\InMemoryEngine\Backend::update
     */
    public function testUpdate()
    {
        $this->assertTrue(
            $this->backend->update( "Content", 2, array( "name" => 'Testing' ) )
        );
        $content = $this->backend->load( "Content", 2 );
        $this->assertEquals( 'Testing', $content->name );
    }

    /**
     * Test updating content with a null value
     *
     * @covers ezp\Persistence\Tests\InMemoryEngine\Backend::update
     */
    public function testUpdateWithNullValue()
    {
        $this->assertTrue(
            $this->backend->update( "Content", 3, array( "name" => null ) )
        );
        $content = $this->backend->load( "Content", 3 );
        $this->assertEquals( null, $content->name );
    }
}
