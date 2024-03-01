<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Sale;
use Cart;
class DetailsComponent extends Component
{
    public $slug;
    public $qty=1;

    public function mount($slug){
        $this->slug=$slug;
    }

    public function increaseQuantity(){
        $this->qty++;
    }

    public function decreaseQuantity(){
       if($this->qty > 1)
        {
            $this->qty--;
        }
    }

    public function store($product_id,$product_name,$product_price){
        Cart::add($product_id,$product_name,$this->qty,$product_price)->associate('App\Models\Product');
        session()->flash('success_message','Item has been added successfully!');
        return redirect()->route('product.cart');
    }

    public function render()
    {
        $product=Product::where('slug',$this->slug)->first();
        $rel_products=Product::where('category_id',$product->category_id)->inRandomOrder()->limit(5)->get();
        $pop_products=Product::inRandomOrder()->limit(4)->get();
        $sale=Sale::find(1);

        return view('livewire.details-component',['product'=>$product,'rel_products'=>$rel_products,'pop_products'=>$pop_products,'sale'=>$sale])->layout('layouts.base');
    }
}
