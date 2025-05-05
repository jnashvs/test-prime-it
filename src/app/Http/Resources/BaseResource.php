<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class BaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if(is_bool($this->resource)){
            return [
                'success' => $this->resource
            ];
        }
        else {
            if(!empty($this->resource) && !is_array($this->resource)) {
                if($this->resource instanceof Collection){
                    return $this->resource->transform(function($item){
                        return $this->process($item);
                    });
                }
                else {
                    return $this->process($this->resource);
                }
            }elseif (is_float($this->resource)){
                return $this->process($this->resource);
            }
            else{
                return parent::toArray($request);
            }
        }
    }

    /** Allows the mutation of the given resource
     * @param $item
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function process($item){
        return parent::toArray($item);
    }

    public function with($request){
        return [
            'code'=> 200
        ];
    }
}
