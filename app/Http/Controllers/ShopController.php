<?php

namespace App\Http\Controllers;

use App\Models\ShopItem;
use App\Models\UserItem;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

       
        $items = ShopItem::whereNull('pet_type_restriction')
                        ->orWhere('pet_type_restriction', $user->pet_type)
                        ->get();

        $myItems = $user->items()->pluck('shop_item_id')->toArray();

        return view('shop.index', compact('items', 'myItems'));
    }

    
    public function inventory()
    {
        /** @var User $user */
        $user = Auth::user();

        
        $myInventory = $user->items()->with('item')->get();

        return view('shop.inventory', compact('myInventory'));
    }

    /**
     * Compra un item.
     */
    public function buy($itemId, GamificationService $gamification)
    {
        /** @var User $user */
        $user = Auth::user();
        $item = ShopItem::findOrFail($itemId);

        if ($user->items()->where('shop_item_id', $itemId)->exists()) {
            return back()->with('error', '¡Ya tienes este objeto!');
        }

        if (!$gamification->spend($user, $item->price)) {
            return back()->with('error', 'No tienes suficientes monedas.');
        }

        UserItem::create([
            'user_id' => $user->id,
            'shop_item_id' => $item->id,
            'is_equipped' => false
        ]);

        $gamification->earn($user, 'buy_item'); // XP por comprar

        
        return redirect()->route('shop.inventory')->with('success', '¡Compra exitosa! Ahora puedes equiparlo.');
    }

   
    public function toggleEquip($userItemId)
    {
        /** @var User $user */
        $user = Auth::user();
        
        
        $userItem = UserItem::where('id', $userItemId)
                            ->where('user_id', $user->id)
                            ->firstOrFail();

        
        if (!$userItem->is_equipped) {
            $categoryToUnequip = $userItem->item->category;
            
            
            foreach ($user->items as $inventoryItem) {
                if ($inventoryItem->is_equipped && $inventoryItem->item->category === $categoryToUnequip) {
                    $inventoryItem->is_equipped = false;
                    $inventoryItem->save();
                }
            }

            $userItem->is_equipped = true;
            $message = '¡Te has puesto: ' . $userItem->item->name . '!';
        } else {
           
            $userItem->is_equipped = false;
            $message = 'Te has quitado: ' . $userItem->item->name;
        }

        $userItem->save();

        return back()->with('success', $message);
    }
}