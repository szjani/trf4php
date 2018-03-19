<?php
declare(strict_types=1);

namespace trf4php;

use PHPUnit\Framework\TestCase;

/**
 * @author Janos Szurovecz <szjani@szjani.hu>
 */
class AbstractObservableTransactionManagerTest extends TestCase
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
        $this->observer = $this->getMockBuilder(TransactionManagerObserver::class)->getMock();
        $this->tm = $this->getMockBuilder(AbstractObservableTransactionManager::class)
            ->setMethods(['commitInner', 'beginTransactionInner', 'rollbackInner', 'transactionalInner'])
            ->getMock();
    }

    public function testAttach()
    {
        $this->observer
            ->expects(self::never())
            ->method('update');
        $this->tm->commit();
        self::assertFalse($this->tm->contains($this->observer));

        $this->observer = $this->getMockBuilder(TransactionManagerObserver::class)->getMock();
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
