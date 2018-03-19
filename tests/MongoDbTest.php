<?php

namespace Spatie\DbDumper\Test;

use PHPUnit\Framework\TestCase;
use Spatie\DbDumper\Databases\MongoDb;
use Spatie\DbDumper\Exceptions\CannotStartDump;

class MongoDbTest extends TestCase
{
    /** @test */
    public function it_provides_a_factory_method()
    {
        $this->assertInstanceOf(MongoDb::class, MongoDb::create());
    }

    /** @test */
    public function it_will_throw_an_exception_when_no_credentials_are_set()
    {
        $this->expectException(CannotStartDump::class);

        MongoDb::create()->dumpToFile('test.gz');
    }

    /** @test */
    public function it_will_throw_an_exception_when_no_credentials_are_set_with_custom_dump_file_name()
    {
        $this->expectException(CannotStartDump::class);

        MongoDb::create()
            ->setDumpFile('test.gz')
            ->dump();
    }

    /** @test */
    public function it_will_throw_an_exception_when_no_dump_file_name_is_set()
    {
        $this->expectException(CannotStartDump::class);

        MongoDb::create()
            ->setDbName('dbname')
            ->dump();
    }

    /** @test */
    public function it_can_generate_a_dump_command()
    {
        $dumpCommand = MongoDb::create()
            ->setDbName('dbname')
            ->getDumpCommand('dbname.gz');

        $this->assertSame('\'mongodump\' --db dbname'
            .' --archive=dbname.gz --host localhost --port 27017', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_compression_enabled()
    {
        $dumpCommand = MongoDb::create()
            ->setDbName('dbname')
            ->enableCompression()
            ->getDumpCommand('dbname.gz');

        $this->assertSame('\'mongodump\' --db dbname'
            .' --archive=dbname.gz --host localhost --port 27017 --gzip', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_username_and_password()
    {
        $dumpCommand = MongoDb::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->getDumpCommand('dbname.gz');

        $this->assertSame('\'mongodump\' --db dbname --archive=dbname.gz'
            .' --username \'username\' --password \'password\' --host localhost --port 27017', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_command_with_custom_host_and_port()
    {
        $dumpCommand = MongoDb::create()
            ->setDbName('dbname')
            ->setHost('mongodb.test.com')
            ->setPort(27018)
            ->getDumpCommand('dbname.gz');

        $this->assertSame('\'mongodump\' --db dbname --archive=dbname.gz'
         .' --host mongodb.test.com --port 27018', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_backup_command_for_a_single_collection()
    {
        $dumpCommand = MongoDb::create()
            ->setDbName('dbname')
            ->setCollection('mycollection')
            ->getDumpCommand('dbname.gz');

        $this->assertSame('\'mongodump\' --db dbname --archive=dbname.gz'
            .' --host localhost --port 27017 --collection mycollection', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_custom_binary_path()
    {
        $dumpCommand = MongoDb::create()
            ->setDbName('dbname')
            ->setDumpBinaryPath('/custom/directory')
            ->getDumpCommand('dbname.gz');

        $this->assertSame('\'/custom/directory/mongodump\' --db dbname --archive=dbname.gz'
            .' --host localhost --port 27017', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_authentication_database()
    {
        $dumpCommand = MongoDb::create()
            ->setDbName('dbname')
            ->setAuthenticationDatabase('admin')
            ->getDumpCommand('dbname.gz');

        $this->assertSame('\'mongodump\' --db dbname --archive=dbname.gz'
            .' --host localhost --port 27017 --authenticationDatabase admin', $dumpCommand);
    }

    /** @test */
    public function it_can_get_the_dump_file_name()
    {
        $dumper = MongoDb::create()->setDumpFile('dump1.gz');

        $this->assertSame('dump1.gz', $dumper->getDumpFile());
    }
}
