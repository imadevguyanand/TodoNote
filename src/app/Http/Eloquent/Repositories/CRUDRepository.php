<?php

namespace App\Http\Eloquent\Repositories;

use App\Http\Eloquent\Interfaces\CRUDInterface;

class CRUDRepository implements CRUDInterface
{
    /**
     * @var $model
     */
    private $model;

    /**
     * Repository constructor
     *
     * @param App\Model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->get();
    }

    public function getById($id)
    {
        return $this->model->findorfail($id);
    }

    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    public function update($id, array $attributes)
    {
        return $this->model->findorfail($id)->update($attributes);
    }

    public function delete($id)
    {
        return $this->model->findorfail($id)->delete();
    }
}
