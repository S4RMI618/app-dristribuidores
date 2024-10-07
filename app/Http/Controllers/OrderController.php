<?php

namespace App\Http\Controllers;

use App\Models\CustomerDetail;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Los administradores ven todas las órdenes, los distribuidores solo las suyas
        $orders = $user->role === 'admin' 
            ? Order::paginate(15)  // Paginar con 15 items por página
            : Order::where('user_id', $user->id)->paginate(15);
    
        return view('orders.index', compact('orders'));
    }
    // Método para mostrar una orden específica
    public function show(Order $order)
    {
        $user = Auth::user();

        // Verifica si el usuario es un distribuidor que intenta acceder a su propia orden
        if ($user->role->name === 'distributor' && $user->id !== $order->user_id) {
            abort(403, 'No tienes permiso para ver esta orden.');
        }

        return view('orders.show', compact('order'));
    }

    public function create()
    {
        $products = Product::all();
        $customers = CustomerDetail::all();

        return view('orders.create', compact('products', 'customers'));
    }


    public function store(Request $request)
    {
        // Validar los datos enviados desde el formulario
        $request->validate([
            'customer_id' => 'required|exists:customer_details,id',
            'products' => 'required|array',
            'products.*' => 'exists:products,id',
            'quantities' => 'required|array',
            'quantities.*' => 'numeric|min:1',
        ]);

        // Crear una nueva orden
        $order = new Order();
        $order->user_id = Auth::id(); // Usuario autenticado (distribuidor)
        $order->customer_id = $request->customer_id;
        $order->status = 'pendiente';
        $order->subtotal = 0; // Se calculará después
        $order->total_tax = 0; // Se calculará después
        $order->total = 0; // Se calculará después
        $order->save();

        // Variables para acumular el subtotal, impuestos y total
        $subtotal = 0;
        $totalTax = 0;

        // Procesar cada producto agregado a la orden
        foreach ($request->products as $index => $productId) {
            $product = Product::findOrFail($productId);
            $quantity = $request->quantities[$index];
            $priceWithTax = $product->getPriceWithTax();

            $lineSubtotal = $product->base_price * $quantity;
            $lineTotalTax = ($priceWithTax - $product->base_price) * $quantity;
            $lineTotal = $priceWithTax * $quantity;

            // Agregar los productos a la orden (en la tabla pivote)
            $order->products()->attach($product->id, [
                'quantity' => $quantity,
                'subtotal' => $lineSubtotal,
                'total_tax' => $lineTotalTax,
                'total' => $lineTotal,
            ]);

            // Actualizar el subtotal y los impuestos
            $subtotal += $lineSubtotal;
            $totalTax += $lineTotalTax;
        }

        // Actualizar los totales de la orden
        $order->subtotal = $subtotal;
        $order->total_tax = $totalTax;
        $order->total = $subtotal + $totalTax;
        $order->save();

        return redirect()->route('orders.index')->with('success', 'Order created successfully!');
    }


    // Método para eliminar una orden
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Orden eliminada exitosamente.');
    }
}
