<?php
declare(strict_types=1);

namespace trf4php;

/**
 * @author Janos Szurovecz <szjani@szjani.hu>
 */
interface TransactionManagerObserver
{
    public function update(ObservableTransactionManager $manager, $event) : void;
}
