<?php

declare(strict_types=1);

class Transaction
{
    public function __construct(
        private int $id,
        private string $type,
        private float $amount
    ) {
    }

    public function process(float &$balance): bool
    {
        return match ($this->type) {
            'deposit' => $this->deposit($balance),
            'withdraw' => $this->withdraw($balance),
            default => false,
        };
    }

    private function deposit(float &$balance): bool
    {
        $balance += $this->amount;

        return true;
    }

    private function withdraw(float &$balance): bool
    {
        if ($this->amount > $balance) {
            return false;
        }

        $balance -= $this->amount;

        return true;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}