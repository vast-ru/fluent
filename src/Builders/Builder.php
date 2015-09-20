<?php

namespace LaravelDoctrine\Fluent\Builders;

use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use LaravelDoctrine\Fluent\Buildable;
use LaravelDoctrine\Fluent\Fluent;
use LaravelDoctrine\Fluent\Relations\ManyToMany;
use LaravelDoctrine\Fluent\Relations\ManyToOne;
use LaravelDoctrine\Fluent\Relations\OneToMany;
use LaravelDoctrine\Fluent\Relations\OneToOne;
use LaravelDoctrine\Fluent\Relations\Relation;
use LogicException;

class Builder extends AbstractBuilder implements Fluent
{
    /**
     * @var array|Buildable[]
     */
    protected $queued = [];

    /**
     * @var array
     */
    protected $macros = [];

    /**
     * @param string|callable $name
     * @param callable|null   $callback
     *
     * @return Table
     */
    public function table($name, callable $callback = null)
    {
        if ($this->isEmbeddedClass()) {
            throw new LogicException;
        }

        $table = new Table($this->builder);

        if (is_callable($name)) {
            $name($table);
        } else {
            $table->setName($name);
        }

        if (is_callable($callback)) {
            $callback($table);
        }

        return $table;
    }

    /**
     * @param callable|null $callback
     *
     * @return Entity
     */
    public function entity(callable $callback = null)
    {
        if ($this->isEmbeddedClass()) {
            throw new LogicException;
        }

        $entity = new Entity($this->builder);

        if (is_callable($callback)) {
            $callback($entity);
        }

        return $entity;
    }

    /**
     * @param          $type
     * @param          $name
     * @param callable $callback
     *
     * @return Field
     */
    public function field($type, $name, callable $callback = null)
    {
        $field = Field::make($this->builder, $type, $name);

        if (is_callable($callback)) {
            $callback($field);
        }

        $this->queue($field);

        return $field;
    }

    /**
     * @param               $name
     * @param callable|null $callback
     *
     * @return Field
     */
    public function increments($name, callable $callback = null)
    {
        if ($this->isEmbeddedClass()) {
            throw new LogicException;
        }

        $field = $this->field(Type::INTEGER, $name, $callback);

        $field->primary()->unsigned()->autoIncrement();

        return $field;
    }

    /**
     * @param          $name
     * @param callable $callback
     *
     * @return Field
     */
    public function string($name, callable $callback = null)
    {
        return $this->field(Type::STRING, $name, $callback);
    }

    /**
     * @param string        $field
     * @param string        $entity
     * @param callable|null $callback
     *
     * @return OneToOne
     */
    public function hasOne($field, $entity, callable $callback = null)
    {
        return $this->oneToOne($field, $entity, $callback);
    }

    /**
     * @param string        $field
     * @param string        $entity
     * @param callable|null $callback
     *
     * @return OneToOne
     */
    public function oneToOne($field, $entity, callable $callback = null)
    {
        return $this->addRelation(
            new OneToOne(
                $this->builder,
                $this->namingStrategy,
                $field,
                $entity
            ),
            $callback
        );
    }

    /**
     * @param               $field
     * @param               $entity
     * @param callable|null $callback
     *
     * @return ManyToOne
     */
    public function belongsTo($field, $entity, callable $callback = null)
    {
        return $this->manyToOne($field, $entity, $callback);
    }

    /**
     * @param string   $field
     * @param string   $entity
     * @param callable $callback
     *
     * @return ManyToOne
     */
    public function manyToOne($field, $entity, callable $callback = null)
    {
        return $this->addRelation(
            new ManyToOne(
                $this->builder,
                $this->namingStrategy,
                $field,
                $entity
            ),
            $callback
        );
    }

    /**
     * @param               $field
     * @param               $entity
     * @param callable|null $callback
     *
     * @return OneToMany
     */
    public function hasMany($field, $entity, callable $callback = null)
    {
        return $this->oneToMany($field, $entity, $callback);
    }

    /**
     * @param string   $field
     * @param string   $entity
     * @param callable $callback
     *
     * @return OneToMany
     */
    public function oneToMany($field, $entity, callable $callback = null)
    {
        return $this->addRelation(
            new OneToMany(
                $this->builder,
                $this->namingStrategy,
                $field,
                $entity
            ),
            $callback
        );
    }

    /**
     * @param               $field
     * @param               $entity
     * @param callable|null $callback
     *
     * @return ManyToMany
     */
    public function belongsToMany($field, $entity, callable $callback = null)
    {
        return $this->manyToMany($field, $entity, $callback);
    }

    /**
     * @param string   $field
     * @param string   $entity
     * @param callable $callback
     *
     * @return ManyToMany
     */
    public function manyToMany($field, $entity, callable $callback = null)
    {
        return $this->addRelation(
            new ManyToMany(
                $this->builder,
                $this->namingStrategy,
                $field,
                $entity
            ),
            $callback
        );
    }

    /**
     * Adds a custom relation to the entity.
     *
     * @param \LaravelDoctrine\Fluent\Relations\Relation $relation
     * @param callable|null                              $callback
     *
     * @return Relation
     */
    public function addRelation(Relation $relation, callable $callback = null)
    {
        if (is_callable($callback)) {
            $callback($relation);
        }

        $this->queue($relation);

        return $relation;
    }

    /**
     * @return bool
     */
    public function isEmbeddedClass()
    {
        return $this->builder->getClassMetadata()->isEmbeddedClass;
    }

    /**
     * @return array|Buildable[]
     */
    public function getQueued()
    {
        return $this->queued;
    }

    /**
     * @param Buildable $buildable
     */
    protected function queue(Buildable $buildable)
    {
        $this->queued[] = $buildable;
    }

    /**
     * @param string        $method
     * @param callable|null $callback
     */
    public function macro($method, callable $callback = null)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('Fluent builder should be extended with a closure argument, none given');
        }

        $this->macros[$method] = $callback;
    }

    /**
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public function __call($method, $params)
    {
        if (isset($this->macros[$method])) {

            // Add builder as first closure param, append the given params
            array_unshift($params, $this);

            return call_user_func_array($this->macros[$method], $params);
        }

        throw new InvalidArgumentException('Fluent builder method [' . $method . '] does not exist');
    }
}
