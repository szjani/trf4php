<?php
/*
 * Copyright (c) 2012 Szurovecz János
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace trf4php;

use trf4php\TransactionManagerObserver;

/**
 * @author Szurovecz János <szjani@szjani.hu>
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
    public function attach(TransactionManagerObserver $observer);

    /**
     * Detach an observer.
     *
     * @param TransactionManagerObserver $observer
     */
    public function detach(TransactionManagerObserver $observer);

    /**
     * Check wheter $observer is attached or not.
     *
     * @param TransactionManagerObserver $observer
     */
    public function contains(TransactionManagerObserver $observer);
}
