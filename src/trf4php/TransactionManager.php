<?php
declare(strict_types=1);

namespace trf4php;

use Closure;
use Exception;

/**
 * Interface to be able to handle transactions.
 *
 * @author Janos Szurovecz <szjani@szjani.hu>
 */
interface TransactionManager
{
    /**
     * Begin a transaction.
     */
    public function beginTransaction() : void;

    /**
     * Commit a transaction.
     *
     * @throws TransactionException
     */
    public function commit() : void;

    /**
     * Rollback a transaction.
     *
     * @throws TransactionException
     */
    public function rollback() : void;

    /**
     * Wrapper method, $func will be executed inside a transaction.
     *
     * @param Closure $func
     * @return mixed The return value of $func
     * @throws Exception Thrown by $func
     * @throws TransactionException
     */
    public function transactional(Closure $func);
}
