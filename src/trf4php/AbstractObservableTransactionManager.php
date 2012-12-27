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

use Closure;
use Exception;
use lf4php\LoggerFactory;
use SplObjectStorage;

/**
 * Send events before and after method calls.
 * POST_* events are only sent if the method executed without any errors.
 *
 * @author Szurovecz János <szjani@szjani.hu>
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

    abstract protected function commitInner();

    abstract protected function beginTransactionInner();

    abstract protected function rollbackInner();

    abstract protected function transactionalInner(Closure $func);

    public function attach(TransactionManagerObserver $observer)
    {
        $this->observers->attach($observer);
    }

    public function detach(TransactionManagerObserver $observer)
    {
        $this->observers->detach($observer);
    }

    public function contains(TransactionManagerObserver $observer)
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

    final public function beginTransaction()
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

    final public function commit()
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

    final public function rollback()
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

    final public function transactional(Closure $func)
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
