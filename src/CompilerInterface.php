<?php

namespace Gnorm;

/**
 * Interface CompilerInterface
 *
 * This interface defines a contract for compiler implementations.
 */
interface CompilerInterface
{
    /**
     * Execute the compiler process.
     *
     * This method should be implemented to perform the necessary
     * compilation tasks.
     *
     * @return void
     */
    public function execute(): void;
}
