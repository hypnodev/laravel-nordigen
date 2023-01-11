<?php

namespace Hypnodev\LaravelNordigen\Traits;

use Hypnodev\LaravelNordigen\Exceptions\NordigenAccountException;
use Hypnodev\LaravelNordigen\Facades\LaravelNordigen;
use Hypnodev\LaravelNordigen\Models\NordigenRequisition;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

trait HasNordigen
{
    public function nordigenRequisition(): HasMany
    {
        return $this->hasMany(NordigenRequisition::class);
    }

    public function createRequisitionUrl(string $institutionId): string
    {
        return LaravelNordigen::requestRequisitionForInstitution($institutionId, $this->id);
    }

    public function nordigenAccounts(?string $nordigenRequisitionReference = null): Collection
    {
        throw_if(!$this->nordigenRequisition()->count(), NordigenAccountException::class, "No requisitions are stored for user [$this->id]");

        $nordigenRequisition = $nordigenRequisitionReference === null
            ? $this->nordigenRequisition()->first()
            : $this->nordigenRequisition()->where('reference', $nordigenRequisitionReference)->first();
        throw_if($nordigenRequisition === null, NordigenAccountException::class, "No requisition with reference [$nordigenRequisitionReference] was found");

        $requisition = LaravelNordigen::nordigenClient()->requisition->getRequisition($nordigenRequisition->reference);
        return collect($requisition['accounts']);
    }

    public function nordigenAccount(string $accountId, array $info = ['metadata', 'balances', 'details', 'transactions'], array $transactionsRange = []): Collection {
        $account = LaravelNordigen::account($accountId);
        $data = [];

        if (in_array('metadata', $info, true)) {
            $data['metadata'] = collect($account->getAccountMetaData());
        }

        if (in_array('balances', $info, true)) {
            $data['balances'] = collect($account->getAccountBalances()['balances']);
        }

        if (in_array('details', $info, true)) {
            $data['details'] = collect($account->getAccountDetails()['account']);
        }

        if (in_array('transactions', $info, true)) {
            $data['transactions'] = match (count($transactionsRange)) {
                1 => $account->getAccountTransactions(head($transactionsRange)),
                2 => $account->getAccountTransactions(head($transactionsRange), last($transactionsRange)),
                default => $account->getAccountTransactions(),
            };

            $data['transactions'] = collect($data['transactions']['transactions']);
        }

        return collect($data);
    }
}
