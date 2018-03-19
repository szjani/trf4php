<?php
declare(strict_types=1);

namespace trf4php;

/**
 * @author Janos Szurovecz <szjani@szjani.hu>
 */
interface ObservableTransactionManager extends TransactionManager
{
    const PRE_COMMIT = 'preCommit';
    const POST_COMMIT = 'postCommit';
    const ERROR_COMMIT = 'errorCommit';

    const PRE_BEGIN_TRANSACTION = 'preBeginTransaction';
    const POST_BEGIN_TRANSACTION = 'postBeginTransaction';
    const ERROR_BEGIN_TRANSACTION = 'errorBeginTransaction';

    const PRE_ROLLBACK = 'preRollback';
    const POST_ROLLBACK = 'postRollback';
    const ERROR_ROLLBACK = 'errorRollback';

    const PRE_TRANSACTIONAL = 'preTransactional';
    const POST_TRANSACTIONAL = 'postTransactional';
    const ERROR_TRANSACTIONAL = 'errorTransactional';

    /**
     * Attach an observer.
     *
     * @param TransactionManagerObserver $observer
     */
    public function attach(TransactionManagerObserver $observer) : void;

    /**
     * Detach an observer.
     *
     * @param TransactionManagerObserver $observer
     */
    public function detach(TransactionManagerObserver $observer) : void;

    /**
     * Check whether $observer is attached or not.
     *
     * @param TransactionManagerObserver $observer
     * @return bool
     */
    public function contains(TransactionManagerObserver $observer) : bool;
}
