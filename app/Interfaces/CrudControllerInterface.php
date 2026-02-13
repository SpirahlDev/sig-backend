<?php

namespace App\interfaces;


use Illuminate\Http\Request;

interface CrudControllerInterface
{
    public function index(Request $request);
    public function show($id);
    public function store(Request $request);
    public function update(Request $request, $id);
    public function destroy($id);
    public function softDelete($id);
    public function toggleStatus($id, $status);
    public function stats();
    public function trash(Request $request);
}
