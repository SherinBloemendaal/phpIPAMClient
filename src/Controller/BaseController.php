<?php

declare(strict_types=1);

namespace SherinBloemendaal\PhpIPAMClient\Controller;

use function SherinBloemendaal\PhpIPAMClient\phpipamConnection;

abstract class BaseController
{
    protected static string $controllerName = '';
    public static bool $defaultAsObject = true;

    /*
     * Common functions in controller
     */
    public function __construct(array $params = [])
    {
        $this->setParams($params);
    }

    public static function getAll(): array
    {
        $response = static::_getStatic();
        if (null === $response->getData() || empty($response->getData())) {
            return [];
        }
        $objects = [];

        foreach ($response->getData() as $object) {
            $objects[] = new static($object);
        }

        return $objects;
    }

    public static function getByID(int $id): static
    {
        return new static(static::_getStatic([$id])->getData());
    }

    public static function post(array $params): static
    {
        // Params that could be converted from objects to id
        $params = static::transformParamsToIDs($params);
        $response = static::_postStatic([], $params);
        $id = $response->getBody()['id'];

        return static::getByID($id);
    }

    public function patch(array $params = []): bool
    {
        $this->setParams($params);
        $params = $this->getParams();

        return $this->_patch([], $params)->isSuccess();
    }

    public function delete(): bool
    {
        return $this->_delete([], ['id' => $this->getId()])->isSuccess();
    }

    abstract public function getId(): int;

    /*
     * Request functions
     */
    protected function _get(array $identifier = [], array $params = []): mixed
    {
        return static::_getStatic($identifier, $params);
    }

    protected static function _getStatic(array $identifier = [], array $params = []): mixed
    {
        return phpipamConnection()->call('get', static::$controllerName, $identifier, $params);
    }

    protected function _post(array $identifier = [], array $params = []): mixed
    {
        return static::_postStatic($identifier, $params);
    }

    protected static function _postStatic(array $identifier = [], array $params = []): mixed
    {
        return phpipamConnection()->call('post', static::$controllerName, $identifier, $params);
    }

    protected function _patch(array $identifier = [], array $params = []): mixed
    {
        return static::_patchStatic($identifier, $params);
    }

    protected function _patchStatic(array $identifier = [], array $params = []): mixed
    {
        return phpipamConnection()->call('patch', static::$controllerName, $identifier, $params);
    }

    protected function _delete(array $identifier = [], array $params = []): mixed
    {
        return static::_deleteStatic($identifier, $params);
    }

    protected static function _deleteStatic(array $identifier = [], array $params = []): mixed
    {
        return phpipamConnection()->call('delete', static::$controllerName, $identifier, $params);
    }

    /**
     * Sets the parameter of the section from array.
     */
    protected function setParams(array $params): void
    {
        foreach ($params as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * Gets all parameter in a array.
     */
    protected function getParams(): array
    {
        $params = get_object_vars($this);
        unset($params['controllerName']);

        return static::transformParamsToIDs($params);
    }

    /**
     * Method turn Objects into their id's in a params array.
     *
     * @param array  $params       The param array
     * @param string $key          The key which should be there at the end
     * @param array  $possibleKeys Possible key in which the Object could stand
     *
     * @return mixed
     */
    public static function getIDFromParams(array $params, string $key, array $possibleKeys, string $class): array
    {
        // Merge keys to one array
        $keys = array_merge($possibleKeys, [$key]);
        foreach ($keys as $k) {
            // check if key exists in params and if its an instance of the given class
            if (\array_key_exists($k, $params) && is_a($params[$k], $class, true)) {
                $params[$key] = $params[$k]->getId();

                // Delete $k if it different from $key
                if ($key !== $k) {
                    unset($params[$k]);
                }

                return $params;
            }
        }

        return $params;
    }

    protected static function getAsObjectOrID(mixed $value, string $class, ?bool $asObject): object|int|null
    {
        if (null === $asObject) {
            $asObject = \call_user_func([$class, 'getDefaultAsObjectValue']);
        }

        if (null === $value) {
            return $value;
        }

        if ($asObject) {
            // Return as Object
            if (is_a($value, $class)) {
                // $value is instance of class
                return $value;
            }

            return \call_user_func([$class, 'getByID'], $value);
        }

        // Return ID
        if (is_a($value, $class)) {
            return $value->getId();
        }

        return $value;
    }

    protected function getDefaultAsObjectValue(): bool
    {
        return (bool) static::$defaultAsObject;
    }

    public function setDefaultAsObjectValue(bool $asObject): void
    {
        static::$defaultAsObject = $asObject;
    }

    abstract protected static function transformParamsToIDs(array $params): array;

    protected static function convertSectionsToID(?array $arr): array
    {
        if (null === $arr || empty($arr)) {
            return [];
        }

        $sections = [];
        foreach ($arr as $section) {
            if ($section instanceof Section) {
                $sections[] = $section->getId();
            } else {
                $sections[] = (int) $section;
            }
        }

        return $sections;
    }
}
