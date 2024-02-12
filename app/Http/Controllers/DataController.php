<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Contract\Database;

class DataController extends Controller
{
    protected $database;
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function index()
    {
        $data =  $this->database->getReference('real_time')->getValue();
        $result = $this->paginate($data);
        return view('home', [
            'data' => $result
        ]);
    }


    public function addData(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'image' => !(isset($request->id)) ? "required" : ""

        ]);

        if ($validate->fails()) {
            return response()->json(['succcess' => false, 'message' => $validate->errors()->first()]);
        }

        try {
            if (isset($request->image) && $request->hasFile('image')) {
                $fileName = time() . '.' . $request->image->extension();
                $request->image->move(public_path('images1'), $fileName);
            }
            if (!isset($request->id)) {
                $postData = [
                    'title' => $request->title,
                    "description" => $request->description,
                    'image' => $fileName,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
                $this->database->getReference('real_time')->push($postData);
                $message = "Data Added";
            } else {
                $updateData = [
                    'title' => $request->title,
                    "description" => $request->description,
                ];
                if (isset($fileName)) {
                    $updateData['image'] = $fileName;
                }
                $this->database->getReference("real_time" . '/' . $request->id)
                    ->update($updateData);
                $message = "Data Updated";
            }
            return response()->json(['success' => true, 'message' => $message]);
        } catch (Exception $e) {
            return response()->json(['succcess' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getData($id)
    {
        return $this->database->getReference("real_time")->getChild($id)->getValue();
    }

    public function remove(Request $request)
    {
        $this->database->getReference("real_time/$request->id")->remove();
        return response()->json(['success' => true, 'message' => 'deleted']);
    }

    public function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
