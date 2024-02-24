<?php

namespace Ucscode\SQuery\Abstract;

use Ucscode\SQuery\Interface\SQueryInterface;
use Ucscode\SQuery\Trait\SQueryTrait;
use Ucscode\SQuery\Condition;
use Ucscode\SQuery\Join;

abstract class AbstractSQuery implements SQueryInterface
{
    use SQueryTrait;

    protected ?string $DMLS = null; // Data Manipulation Language Statement
    protected array $table = [];
    protected array $columns = [];
    protected array $values = [];
    protected array $join = [];
    protected Condition $where;
    protected array $group_by = [];
    protected Condition $having;
    protected array $order_by = [];
    protected ?int $limit = null;
    protected ?int $offset = null;

    public function __construct()
    {
        $this->where = new Condition();
        $this->having = new Condition();
    }

    public function build(): string
    {
        switch($this->DMLS) {
            case self::KEYWORD_SELECT:
                $syntax = $this->buildSelectStatement();
                break;

            case self::KEYWORD_INSERT:
                $syntax = $this->buildInsertStatement();
                break;

            case self::KEYWORD_UPDATE:
                $syntax = $this->buildUpdateStatement();
                break;

            case self::KEYWORD_DELETE:
                $syntax = $this->buildDeleteStatement();
                break;

            default:
                $syntax = [];
        }

        return implode("\n", array_filter($syntax));
    }

    protected function setDMLS(string $dmls)
    {
        if($this->DMLS) {
            throw new \Exception(
                sprintf(
                    "Cannot modify Data Manipulation Language Statement (DMLS). '%s' statement already in use. Please create a new instance of %s for a different operation.",
                    $this->DMLS,
                    get_called_class()
                )
            );
        };
        $this->DMLS = $dmls;
    }

    protected function buildSelectStatement(): array
    {
        $statement = [
            $this->DMLS . ' ' . implode(", ", $this->columns),
            self::KEYWORD_FROM . ' ' . implode(' AS ', $this->table),
        ];

        foreach($this->join as $joinContext) {
            $statement[] = $joinContext['instance']->build($joinContext['prefix']);
        }

        $statement[] = $this->where->build(self::KEYWORD_WHERE);

        $statement[] = empty($this->group_by) ? null : sprintf(
            "%s %s %s",
            self::KEYWORD_GROUP,
            self::KEYWORD_BY,
            implode(", ", $this->group_by)
        );

        $statement[] = $this->having->build(self::KEYWORD_HAVING);

        $statement[] = empty($this->order_by) ? null : sprintf(
            "%s %s %s",
            self::KEYWORD_ORDER,
            self::KEYWORD_BY,
            implode(", ", $this->order_by)
        );

        $statement[] = is_null($this->limit) ? null : sprintf(
            "%s %s",
            self::KEYWORD_LIMIT,
            $this->limit
        );
        
        $statement[] = is_null($this->offset) || is_null($this->limit) ? null : sprintf(
            "%s %s",
            self::KEYWORD_OFFSET,
            $this->offset
        );

        return $statement;
    }

    protected function buildInsertStatement(): array
    {
        return [
            sprintf(
                "%s %s %s",
                $this->DMLS,
                self::KEYWORD_INTO,
                $this->table[0]
            ),
            sprintf('(%s)', implode(", ", $this->columns)),
            'VALUES',
            sprintf('(%s)', implode(", ", $this->values)),
        ];
    }

    protected function buildUpdateStatement(): array
    {
        return [
            sprintf(
                "%s %s %s",
                $this->DMLS,
                $this->table[0],
                self::KEYWORD_SET
            ),
            call_user_func(function () {
                $formation = [];
                foreach(array_combine($this->columns, $this->values) as $key => $value) {
                    $formation[] = "{$key} = {$value}";
                }
                return implode(",\n", $formation);
            }),
            $this->where->build(self::KEYWORD_WHERE)
        ];
    }

    protected function buildDeleteStatement(): array
    {
        return [
            $this->DMLS,
            self::KEYWORD_FROM . ' ' . $this->table[0],
            $this->where->build(self::KEYWORD_WHERE)
        ];
    }

    protected function upsert(string $table, array $data): self
    {
        $this->from($table);
        $this->columns = array_map(fn ($value) => $this->tick($value), array_keys($data));
        $this->values = array_map(function ($value) {
            if(!is_numeric($value)) {
                $value = is_null($value) ? 'NULL' : $this->surround($value, "'");
            }
            return $value;
        }, array_values($data));
        return $this;
    }

    protected function createOrder(string $order, ?string $direction): self
    {
        $context = [
            $this->tick($order),
            $direction ? strtoupper($direction) : $direction
        ];
        $this->order_by[] = implode(" ", array_filter(array_map('trim', $context)));
        return $this;
    }

    protected function appendJoin(string $joinPrefix, Join $join): self
    {
        $this->join[] = [
            'prefix' => sprintf(
                "%s %s",
                $joinPrefix,
                self::KEYWORD_JOIN
            ),
            'instance' => $join
        ];
        return $this;
    }
}
