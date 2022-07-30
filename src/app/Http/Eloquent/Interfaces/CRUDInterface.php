<?php

namespace App\Http\Eloquent\Interfaces;

interface CRUDInterface
{
    function getAll();

    function getById($id);

    function create(array $attributes);

    function update($id, array $attributes);

    function delete($id);
}
