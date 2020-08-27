<?php


namespace App\Classes;


use App\User;
use Illuminate\Support\Arr;
use phpDocumentor\Reflection\Types\Integer;
use Spatie\Permission\Models\Role;

class Crud
{

    public $model;
    public $entities;
    public $columns = [];
    public $fields = [];
    public $object;
    public $mediaPath;
    public $row;


    public function setRow(int $id)
    {
        $this->row = $this->model::find($id);
        return $this;
    }


    public function __construct()
    {
        $this->mediaPath = storage_path('tmp/uploads');
    }


    public function setModel(string $model)
    {
        $this->model = $model;
        $this->object = new $model();
    }


    public function setEntity(string $entities)
    {
        $this->entities = $entities;
    }


    public function setColumns($columns)
    {
        $this->columns = [];

        foreach ($columns as $column) {
            array_push($this->columns,
                [
                    'data' => $column,
                    'name' => $column,
                    'orderable' => 1,
                    'searchable' => 1,
                ]
            );
        }

        return $this;
    }


    public function getFields($key = null)
    {
        if ($key) {
            $values = [];
            for ($i = 0; $i < count($this->fields); $i++)
                $values[] = $this->checkRelationField($this->fields[$i])[$key] ?? '';
            return $values;
        }

        for ($i = 0; $i < count($this->fields); $i++) {

            foreach ($this->fields[$i] as $key => $value) {
                $this->fields[$i] = $this->checkRelationField($this->fields[$i]);
            }
        }


        return $this->fields;
    }


    public function checkRelationField($field)
    {
        if ($field['type'] !== 'relation')
            return $field;


        foreach (array_keys($field) as $key) {
            $relationType = $this->getRelationType($this->object, $field['method']);

            $field['type'] = $this->isMultiple($relationType) ? 'select2_multiple' : 'select2';
            $field['attribute'] = $field['attribute'] ?? 'id';
            $field['model'] = $this->getRelated($this->object, $field['method']);

            if (!$this->isMultiple($relationType))
                $field['name'] = $this->reflectionProperty($this->reflectionMethod($this->object, $field['method']), 'foreignKey');
        }

        return $field;
    }

    public function getValidations()
    {
        $validations = [];

        foreach ($this->fields as $field) {
            $validations[$field['name'] ?? $field['method']] = $field['validation'] ?? null;
        }
        return array_filter($validations);
    }

    public function setColumn($data, $title = null, $orderable = null, $searchable = null)
    {

        array_push($this->columns,
            [
                'data' => $data,
                'name' => $title ?? ucfirst($data),
                'orderable' => $orderable ?? 1,
                'searchable' => $searchable ?? 1,
            ]
        );


        return $this;
    }


    public function setField($field)
    {
        //dd(debug_backtrace());
        array_push($this->fields, $field);
        return $this;
    }


    public function getDatatableColumns()
    {
        $datable_columns = "[";
        foreach ($this->columns as $field):
            $datable_columns .= "{data: '" . $field['data'] . "', name: '" . $field['data'] . "', orderable: " . ($field['orderable'] ? 'true' : 'false') . ", searchable: " . ($field['searchable'] ? 'true' : 'false') . "},";
        endforeach;
        $datable_columns .= "]";

        return $datable_columns;
    }

    public function route($route, $id = null)
    {
        $entities = $this->entities;

        if (in_array($route, ['update', 'show', 'delete']))
            return '/' . $entities . '/' . $id;
        elseif ($route == 'edit')
            return '/' . $entities . '/' . $id . '/edit';
        elseif ($route == 'create')
            return '/' . $entities . '/create';
        elseif (in_array($route, ['index', 'store']))
            return '/' . $entities;
        elseif ($route == 'datatable')
            return '/ajx/' . $entities;
    }


    public function permission($action)
    {
        return $this->entities . '-' . $action;
    }


    public function resetFields()
    {
        $this->fields = [];
        return $this;
    }


    public function setDefaults()
    {


        if (!$this->row)
            return 0;

        for ($i = 0; $i < count($this->fields); $i++) {

            if (in_array($this->fields[$i]['type'], ['select2_multiple'])) {
                continue;
            }

            $name = $this->fields[$i]['name'] ?? $this->fields[$i]['method'];
            $this->fields[$i]['value'] = $this->row->$name ?? null;
        }


    }


    public function isMultiple($relation_type)
    {
        if (in_array($relation_type, [
            'BelongsToMany',
            'HasMany',
            'HasManyThrough',
            'HasOneOrMany',
            'MorphMany',
            'MorphOneOrMany',
            'MorphToMany',
        ]))
            return true;
        else
            return false;

    }


    public function reflectionMethod($object, $methodName)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, []);
    }

    public function reflectionProperty($object, $propertyName)
    {
        $property = new \ReflectionProperty($object, $propertyName);
        $property->setAccessible(true);
        return $property->getValue($object);
    }


    public function getRelationType($object, $methodName)
    {
        //        return get_class($object->{$methodName}()->getRelated());
        $relationType = new \ReflectionClass($object->{$methodName}());
        return $relationType->getShortName();

        //or
        //$oReflectionClass = new \ReflectionClass($object);
        //$method = $oReflectionClass->getMethod($methodName);
        //$relationType = get_class($method->invoke($object));
        //$exploded = explode("\\", $relationType);
        //return end($exploded);
    }


    public function getRelationships()
    {

        //or
        //$reflector = new \ReflectionClass($this->model);
        //$relations = [];
        //foreach ($reflector->getMethods() as $reflectionMethod) {
        //    $returnType = $reflectionMethod->getReturnType();
        //    if ($returnType) {
        //        if (in_array(class_basename($returnType->getName()), ['HasOne', 'HasMany', 'BelongsTo', 'BelongsToMany', 'MorphToMany', 'MorphTo'])) {
        //            $arr = (array)$reflectionMethod;
        //            $relations[$arr['name']] = $returnType->getName();
        //            $relations[] = $reflectionMethod;
        //        }
        //    }
        //}
        //return ($relations);


        $object = new User();
        $reflector = new \ReflectionClass($object);
        $relations = [];


        $public = $reflector->getMethods(\ReflectionMethod::IS_PUBLIC);
        $static = $reflector->getMethods(\ReflectionMethod::IS_STATIC);
        $m = array_diff($public, $static);


        foreach ($m as $reflectionMethod) {
            $methodName = ((array)$reflectionMethod)['name'];
            $method = $reflector->getMethod($methodName);
            $method->setAccessible(true);


            $type = ($method->invoke($object));
            if ((in_array(class_basename($type), ['HasOne', 'HasMany', 'BelongsTo', 'BelongsToMany', 'MorphToMany', 'MorphTo']))) {
                $relations['relationship_type'] = get_class($type);
                $relations['name'] = $methodName;
            }


            return ($relations);

        }
        dd($relations);

    }


    public function getRelated($object, $methodName)
    {
        return get_class($object->{$methodName}()->getRelated());
    }


    public function hasTrait($traitName)
    {
        $traits = class_uses($this->object, true);

        $traits = array_map(function ($n) {
            $class_parts = explode('\\', $n);
            return end($class_parts);
        }, $traits);

        return array_search($traitName, $traits) !== false;
    }


}
