<?php

namespace Spatie\DbDumper\Databases;

use Spatie\DbDumper\DbDumper;
use Spatie\DbDumper\Exceptions\CannotStartDump;
use Symfony\Component\Process\Process;

class Sqlite extends DbDumper
{
    /**
     * Dump the contents of the database.
     *
     * @throws \Spatie\DbDumper\Exceptions\CannotStartDump
     * @throws \Spatie\DbDumper\Exceptions\DumpFailed
     */
    public function dump()
    {
        $this->guardAgainstIncompleteCredentials();

        $dumpFile = $this->getDumpFile();

        $command = $this->getDumpCommand($dumpFile);

        $process = new Process($command);

        if (! is_null($this->timeout)) {
            $process->setTimeout($this->timeout);
        }

        $process->run();

        $this->checkIfDumpWasSuccessFul($process, $dumpFile);
    }

    /**
     * Get the command that should be performed to dump the database.
     *
     * @param string $dumpFile
     *
     * @return string
     */
    public function getDumpCommand(string $dumpFile): string
    {
        return sprintf(
            "echo 'BEGIN IMMEDIATE;\n.dump' | '%ssqlite3' --bail '%s' >'%s'",
            $this->dumpBinaryPath,
            $this->dbName,
            $dumpFile
        );
    }

    protected function guardAgainstIncompleteCredentials()
    {
        foreach (['dumpFile'] as $requiredProperty) {
            if (empty($this->$requiredProperty)) {
                throw CannotStartDump::emptyParameter($requiredProperty);
            }
        }
    }
}
