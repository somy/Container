<?php

class ContainerTest extends PHPUnit_Framework_TestCase
{
    protected static $sampleData = array(
        'dir' => array(
            'current' => './',
            'config' => '../config/',
            'app' => array(
                'root' => '../',
                'boot' => '../app',
                'src' => '../src'
            ),
        ),
        'users' => array(
            'administrator' => array(
                'somy',
                'andriyanto'
            ),
            'contentwriter' => array(
                'a.writer'
            ),
            'regularuser' => array(
                'johndoe'
            ),
            'guest' => array(
                'anonymous'
            )
        ),
        'config' => array(
            'user' => array(
                'type' => array('administrator', 'contentwriter', 'regularuser', 'guest'),
                'acl' => array(
                    'administrator' => array(
                        'news' => array('read', 'write', 'delete', 'edit', 'publish'),
                        'blog' => array('read', 'write', 'delete', 'edit', 'publish'),
                        'user' => array('read', 'write', 'delete', 'edit')
                    ),
                    'contentwriter' => array(
                        'news' => array('read', 'write', 'edit', 'publish'),
                        'blog' => array('read', 'write', 'edit', 'publish'),
                        'user' => array('read'),
                    ),
                    'regularuser' => array(
                        'news' => array('read'),
                        'blog' => array('read', 'write', 'edit', 'publish'),
                        'user' => array()
                    ),
                    'guest' => array(
                        'news' => array('read'),
                        'blog' => array('read'),
                        'user' => array()
                    )
                )
            ),
        ),
        'site' => array(
            'title' => 'This is site titles',
            'description' => 'This is about site descriptions'
        ),
    );

    /**
     * Test array access
     *
     * including set, get, update, delete
     */
    public function testArrayAccess()
    {
        $container =  new \KopiKode\Container();

        /**
         * test array isset/set/get
         */
        $data = self::$sampleData;
        $container['users'] =  $data['users'];
        $this->assertEquals($container['users'], $data['users']);
        $this->assertEquals($container['users.administrator'], $data['users']['administrator']);
        $this->assertTrue(isset($container['users.administrator']));

        $container['data.dir'] = 'data.dir';
        $this->assertEquals($container['data'], array('dir' => 'data.dir'));

        /**
         * test array update/get
         */
        $container['site'] =  $data['site'];
        $this->assertEquals($container['site'], $data['site']);

        $nTitle = 'Update site title';
        $container['site.title'] = $nTitle;
        $this->assertNotEquals($container['site.title'], $data['site']['title']);
        $this->assertEquals($container['site.title'], $nTitle);
        $this->assertEquals($container['site.description'], $data['site']['description']);

        /**
         * test isset false
         */
        unset($container['site.description']);
        $this->assertFalse(isset($container['site.description']));

    }

    /**
     * @expectedException \KopiKode\Exception\InvalidIdentifierException
     *
     * Test Array delete
     */
    public function testInvalidIdentifierException()
    {
        $container =  new \KopiKode\Container();

        $data = self::$sampleData;
        $container['users'] =  $data['users'];

        unset($container['users.guest']);

        //$this->assertArrayHasKey('guest', $container['users']);
        $container['users.guest'];
    }

    /**
     * Test Array Countable
     */
    public function testArrayCountable()
    {
        $container = new \KopiKode\Container();

        $data = self::$sampleData;
        $container['users'] = $data['users'];

        $this->assertEquals(count($container['users']), count($data['users']));

        unset($container['users.guest']);
        $this->assertNotEquals(count($container['users']), count($data['users']));
    }

    /**
     * Test Array Iterator
     */
    public function testArrayIterator()
    {
        $container = new \KopiKode\Container();

        $data = self::$sampleData;
        $container['users'] = $data['users'];

        foreach ($container['users'] as $userType => $member) {
            $this->assertEquals($member, $data['users'][$userType]);
        }
    }

    /**
     * Test Service Dependency Injection
     *
     */
    public function testServiceDI()
    {
        $foo = $this->getMockBuilder('nonexistant')
            ->setMockClassName('Clazz')
            ->setMethods(array('bar'))
            ->getMock();

        $container = new \KopiKode\Container();

        $container['clazz'] = function($c) {
            return new Clazz;
        };

        $this->assertInstanceOf('Clazz', $container['clazz']);
        $this->assertEquals($container['clazz'], $container['clazz']);
    }
}
