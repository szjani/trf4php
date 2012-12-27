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

use PHPUnit_Framework_TestCase;

/**
 * @author Szurovecz János <szjani@szjani.hu>
 */
class AbstractObservableTransactionManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var TransactionManagerObserver
     */
    private $observer;

    /**
     * @var AbstractObservableTransactionManager
     */
    private $tm;

    public function setUp()
    {
        $this->observer = $this->getMock(__NAMESPACE__ . '\TransactionManagerObserver');
        $this->tm = $this->getMock(
            __NAMESPACE__ . '\AbstractObservableTransactionManager',
            array('commitInner', 'beginTransactionInner', 'rollbackInner', 'transactionalInner')
        );
    }

    public function testAttach()
    {
        $this->observer
            ->expects(self::never())
            ->method('update');
        $this->tm->commit();
        self::assertFalse($this->tm->contains($this->observer));

        $this->observer = $this->getMock(__NAMESPACE__ . '\TransactionManagerObserver');
        $this->observer
            ->expects(self::at(0))
            ->method('update')
            ->with($this->tm, ObservableTransactionManager::PRE_COMMIT);
        $this->observer
            ->expects(self::at(1))
            ->method('update')
            ->with($this->tm, ObservableTransactionManager::POST_COMMIT);
        $this->tm->attach($this->observer);
        self::assertTrue($this->tm->contains($this->observer));
        $this->tm->commit();
    }

    public function testDetach()
    {
        $this->tm->attach($this->observer);
        $this->tm->detach($this->observer);
        $this->observer
            ->expects(self::never())
            ->method('update');
        $this->tm->commit();
    }

    public function testBeginTransaction()
    {
        $this->tm->attach($this->observer);
        $this->tm
            ->expects(self::once())
            ->method('beginTransactionInner');
        $this->observer
            ->expects(self::at(0))
            ->method('update')
            ->with($this->tm, ObservableTransactionManager::PRE_BEGIN_TRANSACTION);
        $this->observer
            ->expects(self::at(1))
            ->method('update')
            ->with($this->tm, ObservableTransactionManager::POST_BEGIN_TRANSACTION);
        $this->tm->beginTransaction();
    }

    /**
     * @expectedException \Exception
     */
    public function testBeginTransactionError()
    {
        $this->tm->attach($this->observer);
        $this->tm
            ->expects(self::once())
            ->method('beginTransactionInner')
            ->will(self::throwException(new \Exception()));
        $this->observer
            ->expects(self::at(0))
            ->method('update')
            ->with($this->tm, ObservableTransactionManager::PRE_BEGIN_TRANSACTION);
        $this->observer
            ->expects(self::at(1))
            ->method('update')
            ->with($this->tm, ObservableTransactionManager::ERROR_BEGIN_TRANSACTION);
        $this->tm->beginTransaction();
    }

    public function testRollback()
    {
        $this->tm->attach($this->observer);
        $this->tm
            ->expects(self::once())
            ->method('rollbackInner');
        $this->observer
            ->expects(self::at(0))
            ->method('update')
            ->with($this->tm, ObservableTransactionManager::PRE_ROLLBACK);
        $this->observer
            ->expects(self::at(1))
            ->method('update')
            ->with($this->tm, ObservableTransactionManager::POST_ROLLBACK);
        $this->tm->rollback();
    }

    /**
     * @expectedException \Exception
     */
    public function testRollbackError()
    {
        $this->tm->attach($this->observer);
        $this->tm
            ->expects(self::once())
            ->method('rollbackInner')
            ->will(self::throwException(new \Exception()));
        $this->observer
            ->expects(self::at(0))
            ->method('update')
            ->with($this->tm, ObservableTransactionManager::PRE_ROLLBACK);
        $this->observer
            ->expects(self::at(1))
            ->method('update')
            ->with($this->tm, ObservableTransactionManager::ERROR_ROLLBACK);
        $this->tm->rollback();
    }

    /**
     * @expectedException \Exception
     */
    public function testCommitError()
    {
        $this->tm->attach($this->observer);
        $this->tm
            ->expects(self::once())
            ->method('commitInner')
            ->will(self::throwException(new \Exception()));
        $this->observer
            ->expects(self::at(0))
            ->method('update')
            ->with($this->tm, ObservableTransactionManager::PRE_COMMIT);
        $this->observer
            ->expects(self::at(1))
            ->method('update')
            ->with($this->tm, ObservableTransactionManager::ERROR_COMMIT);
        $this->tm->commit();
    }

    public function testTransactional()
    {
        $this->tm->attach($this->observer);
        $this->tm
            ->expects(self::once())
            ->method('transactionalInner');
        $this->observer
            ->expects(self::at(0))
            ->method('update')
            ->with($this->tm, ObservableTransactionManager::PRE_TRANSACTIONAL);
        $this->observer
            ->expects(self::at(1))
            ->method('update')
            ->with($this->tm, ObservableTransactionManager::POST_TRANSACTIONAL);
        $this->tm->transactional(
            function () {
            }
        );
    }

    /**
     * @expectedException \Exception
     * @throws \Exception
     */
    public function testTransactionalException()
    {
        $this->tm->attach($this->observer);
        $this->tm
            ->expects(self::once())
            ->method('transactionalInner')
            ->will(self::throwException(new \Exception()));
        $this->observer
            ->expects(self::at(0))
            ->method('update')
            ->with($this->tm, ObservableTransactionManager::PRE_TRANSACTIONAL);
        $this->observer
            ->expects(self::at(1))
            ->method('update')
            ->with($this->tm, ObservableTransactionManager::ERROR_TRANSACTIONAL);
        $this->tm->transactional(
            function () {
            }
        );
    }
}
