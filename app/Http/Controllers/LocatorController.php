<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocatorRequest;
use App\Models\Locator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class LocatorController extends Controller
{
    public function index()
    {
        $title = 'Data Locator';
        $breadcrumbs = ['Master', 'Data Locator'];

        return view('locator.index', compact('title', 'breadcrumbs'));
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            $data = Locator::get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="#modal-dialog" id="' . $row->id . '" class="btn btn-sm btn-success btn-edit" data-route="' . route('locators.update', $row->id) . '" data-bs-toggle="modal">Edit</a> <button type="button" data-route="' . route('locators.destroy', $row->id) . '" class="delete btn btn-danger btn-delete btn-sm">Delete</button>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function store(LocatorRequest $locatorRequest)
    {
        try {
            DB::beginTransaction();

            $locator = Locator::create($locatorRequest->all());

            DB::commit();

            return redirect()->route('locators.index')->with('success', "{$locator->nama_locator} berhasil ditambahkan");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function show(Locator $locator)
    {
        return response()->json([
            'status' => 'success',
            'locator' => $locator
        ]);
    }

    public function update(LocatorRequest $locatorRequest, Locator $locator)
    {
        try {
            DB::beginTransaction();

            $locator->update($locatorRequest->all());

            DB::commit();

            return redirect()->route('locators.index')->with('success', "{$locator->nama_locator} berhasil diupdate");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(Locator $locator)
    {
        try {
            DB::beginTransaction();

            $locator->delete();

            DB::commit();

            return redirect()->route('locators.index')->with('success', "{$locator->nama_locator} berhasil dihapus");
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }
}
