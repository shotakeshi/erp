<?php
namespace App\Queries;

use App\Models\Position;

class PositionQuery
{
    public function forSelect()
    {
        return Position::query()
            ->select([ 'id', 'name'])
            ->orderBy('id')
            ->get();
    }

    public function paginate(array $filters)
    {
        return Position::query()
            ->filter($filters)
            ->paginate();
    }
}