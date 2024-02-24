<?php

namespace Ucscode\SQuery\Interface;

use Ucscode\SQuery\Condition;
use Ucscode\SQuery\Join;

interface SQueryInterface extends SQueryConstantInterface
{
    public function build(): string;

    public function select(string|array $columns): self;
    public function update(string $table, array $keyValues): self;
    public function insert(string $table, array $keyValues): self;
    public function delete(): self;

    public function from(string $table, string $alias = null): self;
    public function innerJoin(Join $join): self;
    public function leftJoin(Join $join): self;
    public function rightJoin(Join $join): self;

    public function where(Condition $condition): self;
    public function groupBy(string|array $group): self;
    public function orderBy(string|array $order, string $direction): self;
    public function having(Condition $condition): self;
    public function limit(?int $limit, ?int $offset): self;
    public function offset(?int $offset): self;

    public function getWhereCondition(): Condition;
}