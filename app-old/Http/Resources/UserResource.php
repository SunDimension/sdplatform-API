<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Nette\Utils\DateTime;

class UserResource extends JsonResource
{

    public static $wrap = false;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => (new DateTime($this->created_at))->format('Y-m-d H:i:s'),
            'branch' => $this->branch ? $this->branch->name : null,
            // 'warehouse_id' => $this->warehouse_id,
            // 'warehouse' => $this->warehouse ? $this->warehouse->name : null,
            // 'status' => $this->status ? $this->status->name : null,
            // 'status_id' => $this->status_id,
            'branch_id' => $this->branch_id,
            'store_id' => $this->store_id,
            'store' => $this->store ? $this->store->name : null,
            "roles" => RoleResource::collection($this->whenLoaded("roles"))
        ];
    }
}
