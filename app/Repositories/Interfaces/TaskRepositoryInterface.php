<?php

namespace App\Repositories\Interfaces;

interface TaskRepositoryInterface
{
    public function index();
    public function show();
    public function store();
    public function update();
    public function destroy();
}
