<?php


namespace App\Classes;


class Crud
{

    public $model;
    public $entities;
    public $columns = [];
    public $fields = [];


    public function __construct()
    {
    }

    public function setModel(string $model)
    {
        $this->model = $model;
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
                $values[] = $this->fields[$i][$key] ?? '';
            return $values;
        }


        return $this->fields;
    }

    public function getValidations()
    {
        $validations = [];

        foreach ($this->fields as $field) {
            $validations[$field['name']] = $field['validation'] ?? null;
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
//        dd(debug_backtrace());

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


    public function setDefaults($row)
    {
        for ($i = 0; $i < count($this->fields); $i++) {

            if (in_array($this->fields[$i]['type'], ['select2_multiple'])) {
                continue;
            }

            $name = $this->fields[$i]['name'];
            $this->fields[$i]['value'] = $row->$name ?? null;
        }
    }

    public function getRelationships()
    {
        $reflector = new \ReflectionClass($this->model);
        $relations = [];
        foreach ($reflector->getMethods() as $reflectionMethod) {
            $returnType = $reflectionMethod->getReturnType();
            if ($returnType) {
                if (in_array(class_basename($returnType->getName()), ['HasOne', 'HasMany', 'BelongsTo', 'BelongsToMany', 'MorphToMany', 'MorphTo'])) {
                    $arr = (array)$reflectionMethod;
                    $relations[$arr['name']] = $returnType->getName();
                }
            }
        }
        return ($relations);
    }
}
