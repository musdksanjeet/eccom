<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Models\Product;
use Livewire\WithPagination;
use Cart;
class ShopComponent extends Component
{
    use WithPagination;
    public $sorting;
    public $pagesize;
    public $min_price=1;
    public $max_price=1000;


    public function mount(){
        $this->sorting="default";
        $this->pagesize=12;
    }
    public function emailTo($component, $action)
    {
        $this->emit($action);
    }


    public function store($product_id,$product_name,$product_price){
        Cart::instance('cart')->add($product_id,$product_name,1,$product_price)->associate('App\Models\Product');
        session()->flash('success_message','Item has been added successfully!');
        return redirect()->route('product.cart');
    }

    public function addToWishlist($product_id,$product_name,$product_price)
    {

        Cart::instance('wishlist')->add($product_id,$product_name,1,$product_price)->associate('App\Models\Product');
        $this->emailTo('wishlist-count-component','refreshComponent');

    }

    public function removeFromWishlist($product_id){
        foreach(Cart::instance('wishlist')->content() as $witems){
            if($witems->id == $product_id){
                Cart::instance('wishlist')->remove($witems->rowId);
                $this->emailTo('wishlist-count-component','refreshComponent');

            }
        }
    }

    public function render()
    {
        if($this->sorting=='date'){
            $products = Product::whereBetween('regular_price',[$this->min_price,$this->max_price])->orderBy('created_at','DESC')->paginate($this->pagesize);  
            //SELECT * FROM products WHERE regular_price BETWEEN min_price AND max_price ORDER BY created_at DESC LIMIT page_size OFFSET (page_number - 1) * page_size;
        }

        else if($this->sorting=='price'){
            $products=Product::whereBetween('regular_price',[$this->min_price,$this->max_price])->orderBy('regular_price','ASC')->paginate($this->pagesize);
        }
        else if($this->sorting=='price-desc'){
            $products=Product::whereBetween('regular_price',[$this->min_price,$this->max_price])->orderBy('regular_price','DESC')->paginate($this->pagesize);   
        }
        else{
            $products=Product::whereBetween('regular_price',[$this->min_price,$this->max_price])->paginate($this->pagesize);
        }
        $categories=Category::all();
        return view('livewire.shop-component',['products'=>$products,'categories'=>$categories])->layout('layouts.base');
    }
}
