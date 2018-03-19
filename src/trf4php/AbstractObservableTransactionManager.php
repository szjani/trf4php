<?php
declare(strict_types=1);

namespace trf4php;

use Closure;
use Exception;
use lf4php\LoggerFactory;
use SplObjectStorage;

/**
 * Send events before and after method calls.
 * POST_* events are only sent if the method executed without any errors.
 *
 * @author Janos Szurovecz <szjani@szjani.hu>
 */
abstract class AbstractObservableTransactionManager implements ObservableTransactionManager
{
    private $observers;

    public function __construct()
    {
        $this->observers = new SplObjectStorage();
    }

    private static function getLogger()
    {
        return LoggerFactory::getLogger(__CLASS__);
    }

    abstract protected function commitInner() : void;

    abstract protected function beginTransactionInner() : void;

    abstract protected function rollbackInner() : void;

    abstract protected function transactionalInner(Closure $func) : void;

    public function attach(TransactionManagerObserver $observer) : void
    {
        $this->observers->attach($observer);
    }

    public function detach(TransactionManagerObserver $observer) : void
    {
        $this->observers->detach($observer);
    }

    public function contains(TransactionManagerObserver $observer) : bool
    {
        return $this->observers->contains($observer);
    }

    final protected function notify($event)
    {
        foreach ($this->observers as $observer) {
            try {
                $observer->update($this, $event);
            } catch (Exception $e) {
                self::getLogger()->error('Error during transaction manager observer update!', null, $e);
            }
        }
    }

    final public function beginTransaction() : void
    {
        $this->notify(self::PRE_BEGIN_TRANSACTION);
        try {
            $this->beginTransactionInner();
        } catch (Exception $e) {
            self::getLogger()->error('Error during starting transaction!', null, $e);
            $this->notify(self::ERROR_BEGIN_TRANSACTION);
            throw $e;
        }
        $this->notify(self::POST_BEGIN_TRANSACTION);
    }

    final public function commit() : void
    {
        $this->notify(self::PRE_COMMIT);
        try {
            $this->commitInner();
        } catch (Exception $e) {
            self::getLogger()->error('Error during committing transaction!', null, $e);
            $this->notify(self::ERROR_COMMIT);
            throw $e;
        }
        $this->notify(self::POST_COMMIT);
    }

    final public function rollback() : void
    {
        $this->notify(self::PRE_ROLLBACK);
        try {
            $this->rollbackInner();
        } catch (Exception $e) {
            self::getLogger()->error('Error during rollbacking transaction!', null, $e);
            $this->notify(self::ERROR_ROLLBACK);
            throw $e;
        }
        $this->notify(self::POST_ROLLBACK);
    }

    final public function transactional(Closure $func) : void
    {
        $this->notify(self::PRE_TRANSACTIONAL);
        try {
            $this->transactionalInner($func);
        } catch (Exception $e) {
            self::getLogger()->error('Error during executing transactional method!', null, $e);
            $this->notify(self::ERROR_TRANSACTIONAL);
            throw $e;
        }
        $this->notify(self::POST_TRANSACTIONAL);
    }
}
