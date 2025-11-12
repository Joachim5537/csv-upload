<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class UploadResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'file_name' => $this->file_name,
            'status' => $this->status,
            'created_at' => Carbon::parse($this->created_at)->format('d-m-Y h:i a'),
            'time_ago' => Carbon::parse($this->created_at)->diffForHumans()
        ];
    }
}

