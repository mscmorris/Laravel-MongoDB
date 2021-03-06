<?php
require_once('tests/app.php');

use Jenssegers\Mongodb\Facades\DB;

class QueryTest extends PHPUnit_Framework_TestCase {

	public function setUp() {}

	public function tearDown()
	{
		DB::collection('users')->truncate();
		DB::collection('items')->truncate();
	}

	public function testCollection()
	{
		$this->assertInstanceOf('Jenssegers\Mongodb\Builder', DB::collection('users'));
	}

	public function testInsert()
	{
		$user = array('name' => 'John Doe');
		DB::collection('users')->insert($user);

		$users = DB::collection('users')->get();
		$this->assertEquals(1, count($users));

		$user = DB::collection('users')->first();
		$this->assertEquals('John Doe', $user['name']);
	}

	public function testFind()
	{
		$user = array('name' => 'John Doe');
		$id = DB::collection('users')->insertGetId($user);

		$this->assertNotNull($id);
		$this->assertTrue(is_string($id));

		$user = DB::collection('users')->find($id);
		$this->assertEquals('John Doe', $user['name']);
	}

	public function testSubKey()
	{
		$user1 = array(
			'name' => 'John Doe',
			'address' => array(
				'country' => 'Belgium',
				'city' => 'Ghent'
				)
			);

		$user2 = array(
			'name' => 'Jane Doe',
			'address' => array(
				'country' => 'France',
				'city' => 'Paris'
				)
			);

		DB::collection('users')->insert(array($user1, $user2));

		$users = DB::collection('users')->where('address.country', 'Belgium')->get();
		$this->assertEquals(1, count($users));
		$this->assertEquals('John Doe', $users[0]['name']);
	}

	public function testInArray()
	{
		$item1 = array(
			'tags' => array('tag1', 'tag2', 'tag3', 'tag4')
			);

		$item2 = array(
			'tags' => array('tag2')
			);

		DB::collection('items')->insert(array($item1, $item2));

		$items = DB::collection('items')->where('tags', 'tag2')->get();
		$this->assertEquals(2, count($items));

		$items = DB::collection('items')->where('tags', 'tag1')->get();
		$this->assertEquals(1, count($items));
	}

}