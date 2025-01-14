<?php

namespace App\Http\Controllers;

use App\Http\Resources\ResepListResource;
use App\Models\Bahan;
use App\Models\Resep;
use App\Validators\ResepValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommonController extends Controller
{
    public function getResepList(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Default 10 item per halaman
        $keyword = $request->input('keyword', '');

        $resep_list = Resep::where('nama_resep', 'LIKE', '%' . $keyword . '%')
            ->with('bahans')
            ->paginate($perPage);

        return response()->json([
            'data' => ResepListResource::collection($resep_list->items()),
            'current_page' => $resep_list->currentPage(),
            'last_page' => $resep_list->lastPage(),
            'total' => $resep_list->total()
        ]);
    }

    public function getResep($id)
    {
        $resep = Resep::where('id', $id)->with('bahans')->first();
        if($resep)
        {
            $resep = [
                'id' => $resep->id,
                'nama_resep' => $resep->nama_resep,
                'desc_resep' => $resep->desc_resep,
                'langkah' => explode("|", $resep->langkah),
                'bahans' => $resep->bahans->map(function($data) {
                    return $data->desc_bahan;
                })
            ];
        }
        
        return response()->json($resep);
    }

    public function addResep(Request $request)
    {
        try {
            $input_data = ResepValidator::validate($request);
            if($request->filled('resep_id'))
            {
                if(Resep::where('id', $request->resep_id)->update($input_data))
                {
                    if(Bahan::where('resep_id', $request->resep_id)->delete())
                    {
                        foreach ($request->desc_bahan as $value) {
                            Bahan::create([
                                'resep_id' => $request->resep_id,
                                'desc_bahan' => $value
                            ]);
                        }
                    }
                }

                return response()->json(['msg' => 'Resep diupdate', 'msg_type' => "success"]);
            }else{
                $new_resep = Resep::create($input_data);
                if($new_resep)
                {
                    foreach ($request->desc_bahan as $value) {
                        Bahan::create([
                            'resep_id' => $new_resep->id,
                            'desc_bahan' => $value
                        ]);
                    }
                }

                return response()->json(['msg' => 'Resep disimpan', 'msg_type' => "success"]);
            }
        } catch (\Throwable $th) {
            Log::error('Kesalahan Sistem: ' . $th->getMessage());
            return response()->json(['msg' => 'Terjadi kesalahan', 'msg_type' => "error"], 500);
        }
    }

    public function deleteResep(Request $request)
    {
        try {
            if($request->filled('resep_id'))
            {
                if(Resep::where('id', $request->resep_id)->delete())
                    return response()->json(['msg' => 'Resep Berhasil dihapus', 'msg_type' => "success"]);
                else
                    return response()->json(['msg' => 'Resep Gagal dihapus', 'msg_type' => "warning"],400);
            }

            return response()->json(['msg' => 'Invalid Resep Id', 'msg_type' => "warning"],400);
        } catch (\Throwable $th) {
            Log::error('Kesalahan Sistem: ' . $th->getMessage());
            return response()->json(['msg' => 'Terjadi kesalahan', 'msg_type' => "error"], 500);
        }
    }
}
