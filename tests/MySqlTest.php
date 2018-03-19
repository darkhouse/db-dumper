<?php

namespace Spatie\DbDumper\Test;

use PHPUnit\Framework\TestCase;
use Spatie\DbDumper\Databases\MySql;
use Spatie\DbDumper\Exceptions\CannotStartDump;
use Spatie\DbDumper\Exceptions\CannotSetParameter;

class MySqlTest extends TestCase
{
    /** @test */
    public function it_provides_a_factory_method()
    {
        $this->assertInstanceOf(MySql::class, MySql::create());
    }

    /** @test */
    public function it_will_throw_an_exception_when_no_credentials_are_set()
    {
        $this->expectException(CannotStartDump::class);

        MySql::create()->dumpToFile('test.sql');
    }

    /** @test */
    public function it_will_throw_an_exception_when_no_credentials_are_set_with_custom_dump_file_name()
    {
        $this->expectException(CannotStartDump::class);

        MySql::create()
            ->setDumpFile('test.sql')
            ->dump();
    }

    /** @test */
    public function it_will_throw_an_exception_when_no_dump_file_name_is_set()
    {
        $this->expectException(CannotStartDump::class);

        MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->dump();
    }

    /** @test */
    public function it_can_generate_a_dump_command()
    {
        $dumpCommand = MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --result-file="dump.sql" dbname', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_without_using_comments()
    {
        $dumpCommand = MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->dontSkipComments()
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'mysqldump\' --defaults-extra-file="credentials.txt" --extended-insert --result-file="dump.sql" dbname', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_without_using_extended_insterts()
    {
        $dumpCommand = MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->dontUseExtendedInserts()
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --skip-extended-insert --result-file="dump.sql" dbname', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_custom_binary_path()
    {
        $dumpCommand = MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setDumpBinaryPath('/custom/directory')
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'/custom/directory/mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --result-file="dump.sql" dbname', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_without_using_extending_inserts()
    {
        $dumpCommand = MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->dontUseExtendedInserts()
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --skip-extended-insert --result-file="dump.sql" dbname', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_using_single_transaction()
    {
        $dumpCommand = MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->useSingleTransaction()
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --single-transaction --result-file="dump.sql" dbname', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_a_custom_socket()
    {
        $dumpCommand = MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setSocket(1234)
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --socket=1234 --result-file="dump.sql" dbname', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_for_specific_tables_as_array()
    {
        $dumpCommand = MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->includeTables(['tb1', 'tb2', 'tb3'])
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --result-file="dump.sql" dbname --tables tb1 tb2 tb3', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_for_specific_tables_as_string()
    {
        $dumpCommand = MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->includeTables('tb1 tb2 tb3')
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --result-file="dump.sql" dbname --tables tb1 tb2 tb3', $dumpCommand);
    }

    /** @test */
    public function it_will_throw_an_exception_when_setting_exclude_tables_after_setting_tables()
    {
        $this->expectException(CannotSetParameter::class);

        MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->includeTables('tb1 tb2 tb3')
            ->excludeTables('tb4 tb5 tb6');
    }

    /** @test */
    public function it_can_generate_a_dump_command_excluding_tables_as_array()
    {
        $dumpCommand = MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->excludeTables(['tb1', 'tb2', 'tb3'])
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert '.
                          '--ignore-table=dbname.tb1 --ignore-table=dbname.tb2 --ignore-table=dbname.tb3 --result-file="dump.sql" dbname', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_excluding_tables_as_string()
    {
        $dumpCommand = MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->excludeTables('tb1, tb2, tb3')
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert '.
                          '--ignore-table=dbname.tb1 --ignore-table=dbname.tb2 --ignore-table=dbname.tb3 --result-file="dump.sql" dbname', $dumpCommand);
    }

    /** @test */
    public function it_will_throw_an_exception_when_setting_tables_after_setting_exclude_tables()
    {
        $this->expectException(CannotSetParameter::class);

        MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->excludeTables('tb1 tb2 tb3')
            ->includeTables('tb4 tb5 tb6');
    }

    /** @test */
    public function it_can_generate_the_contents_of_a_credentials_file()
    {
        $credentialsFileContent = MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setHost('hostname')
            ->setSocket(1234)
            ->getContentsOfCredentialsFile();

        $this->assertSame(
            '[client]'.PHP_EOL."user = 'username'".PHP_EOL."password = 'password'".PHP_EOL."host = 'hostname'".PHP_EOL."port = '3306'",
            $credentialsFileContent);
    }

    /** @test */
    public function it_can_get_the_name_of_the_db()
    {
        $dbName = 'testName';

        $dbDumper = MySql::create()->setDbName($dbName);

        $this->assertEquals($dbName, $dbDumper->getDbName());
    }

    /** @test */
    public function it_can_add_extra_options()
    {
        $dumpCommand = MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->addExtraOption('--extra-option')
            ->addExtraOption('--another-extra-option="value"')
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --extra-option --another-extra-option="value" --result-file="dump.sql" dbname', $dumpCommand);
    }

    /** @test */
    public function it_can_get_the_host()
    {
        $dumper = MySql::create()->setHost('myHost');

        $this->assertEquals('myHost', $dumper->getHost());
    }

    /** @test */
    public function it_can_set_db_name_as_an_extra_options()
    {
        $dumpCommand = MySql::create()
            ->setUserName('username')
            ->setPassword('password')
            ->addExtraOption('--extra-option')
            ->addExtraOption('--another-extra-option="value"')
            ->addExtraOption('--databases dbname')
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --extra-option --another-extra-option="value" --databases dbname --result-file="dump.sql"', $dumpCommand);
    }

    /** @test */
    public function it_can_get_the_name_of_the_db_when_dbname_was_set_as_an_extra_option()
    {
        $dbName = 'testName';

        $dbDumper = MySql::create()->addExtraOption("--databases {$dbName}");

        $this->assertEquals($dbName, $dbDumper->getDbName());
    }

    /** @test */
    public function it_can_get_the_name_of_the_db_when_dbname_was_overriden_as_an_extra_option()
    {
        $dbName = 'testName';
        $overridenDbName = 'otherName';

        $dbDumper = MySql::create()->setDbName($dbName)->addExtraOption("--databases {$overridenDbName}");

        $this->assertEquals($overridenDbName, $dbDumper->getDbName());
    }

    /** @test */
    public function it_can_generate_a_dump_command_excluding_tables_as_array_when_dbname_was_set_as_an_extra_option()
    {
        $dumpCommand = MySql::create()
            ->setUserName('username')
            ->setPassword('password')
            ->addExtraOption('--databases dbname')
            ->excludeTables(['tb1', 'tb2', 'tb3'])
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert '.
                          '--ignore-table=dbname.tb1 --ignore-table=dbname.tb2 --ignore-table=dbname.tb3 --databases dbname --result-file="dump.sql"', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_excluding_tables_as_string_when_dbname_was_set_as_an_extra_option()
    {
        $dumpCommand = MySql::create()
            ->setUserName('username')
            ->setPassword('password')
            ->addExtraOption('--databases dbname')
            ->excludeTables('tb1, tb2, tb3')
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert '.
                          '--ignore-table=dbname.tb1 --ignore-table=dbname.tb2 --ignore-table=dbname.tb3 --databases dbname --result-file="dump.sql"', $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_set_gtid_purged()
    {
        $dumpCommand = MySql::create()
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setGtidPurged('OFF')
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $this->assertSame('\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --set-gtid-purged=OFF --result-file="dump.sql" dbname', $dumpCommand);
    }

    /** @test */
    public function it_can_get_the_dump_file_name()
    {
        $dumper = MySql::create()->setDumpFile('dump1.sql');

        $this->assertSame('dump1.sql', $dumper->getDumpFile());
    }
}
