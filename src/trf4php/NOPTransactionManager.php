<?php
declare(strict_types=1);

namespace trf4php;

use Closure;

/**
 * Class NOPTransactionManager. Does not do anything, useful for testing.
 *
 * @package trf4php
 *
 * @author Janos Szurovecz <szjani@szjani.hu>
 */
class NOPTransactionManager extends AbstractObservableTransactionManager
{
    protected function commitInner() : void
    {
        // noop
    }

    protected function beginTransactionInner() : void
    {
        // noop
    }

    protected function rollbackInner() : void
    {
        // noop
    }

    protected function transactionalInner(Closure $func) : void
    {
        // noop
    }
}
