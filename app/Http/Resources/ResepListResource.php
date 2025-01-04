<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResepListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $bahanResep = $this->bahans;
        if ($bahanResep->count() > 3) {
            $bahans = $bahanResep->slice(0, 3)->pluck('desc_bahan')->implode(', ') . '...';
        } else {
            $bahans = $bahanResep->pluck('desc_bahan')->implode(', ');
        }

        return [
            'id' => $this->id,
            'nama' => $this->nama_resep,
            'deskripsi' => $this->desc_resep,
            'langkah' => $this->langkah,
            'bahan' => $bahans
        ];
    }
}
