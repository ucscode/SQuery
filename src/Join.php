<?php

namespace Ucscode\SQuery;

use Ucscode\SQuery\Interface\SQueryConstantInterface;
use Ucscode\SQuery\Interface\SQueryInterface;

class Join
{
    protected SQueryInterface $reference;
    protected Condition $on;
    protected ?string $alias = null;
    
    public function __construct(string|SQueryInterface $reference = null, Condition $on, ?string $alias = null)
    {
        $this->setReference($reference);
        $alias ??= (is_string($reference) ? $reference : null);
        $this->alias = $alias;
        $this->on = $on;
    }

    public function setReference(string|SQueryInterface $reference): self
    {
        if(is_string($reference)) {
            $reference = (new SQuery())
                ->select()
                ->from($reference);
        }
        $this->reference = $reference;
        return $this;
    }

    public function getReference(): ?SQueryInterface
    {
        return $this->reference;
    }

    public function setAlias(?string $alias): self
    {
        $this->alias = $alias;
        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setOn(Condition $on): self
    {
        $this->on = $on;
        return $this;
    }

    public function getOn(): ?Condition
    {
        return $this->on;
    }

    public function build(?string $keyword = null): string
    {
        $statement = [
            sprintf(
                "%s (%s)",
                $keyword,
                $this->reference->build(),
            )
        ];
        
        $statement[] = !$this->alias ? null : sprintf(
            "%s `%s`",
            SQueryConstantInterface::KEYWORD_AS,
            $this->alias
        );

        $statement[] = sprintf(
            "%s (%s)",
            SQueryConstantInterface::KEYWORD_ON,
            $this->on->build()
        );

        return trim(implode("\n", array_filter($statement)));
    }
}