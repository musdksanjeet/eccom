<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Cart;

class WishlistComponent extends Component
{
    public function moveProductFromWishlistToCart($rowId)
    {
        $item = Cart::instance('wishlist')->get($rowId); // Fix: Change $items to $item
        Cart::instance('wishlist')->remove($rowId);
        Cart::instance('cart')->add($item->id, $item->name, 1, $item->price)->associate('App\Models\Product');
        $this->emailTo('wishlist-count-component', 'refreshComponent');
        $this->emitTo('cart-count-component', 'refreshComponent');
    }

    // Define the emailTo method
    public function emailTo($component, $action)
    {
        $this->emit($action);
    }

    public function render()
    {
        return view('livewire.wishlist-component')->layout('layouts.base');
    }
}
