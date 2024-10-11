<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Orden') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="p-4 py-5 sm:px-6">
                    <form action="{{ route('orders.update', $order->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Selección del Cliente -->

                        <div>
                            <x-input-label for="customer_id" :value="__('Seleccionar Cliente')" />
                            <select id="customer_id" name="customer_id" class="block mt-1 w-full rounded-md" required>
                                <option value="">{{ __('Seleccione un cliente') }}</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        {{ $customer->id == $order->customer_id ? 'selected' : '' }}>
                                        {{ $customer->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
                        </div>

                        <div class="block mt-1">
                            <label for="status" class="block text-sm font-medium text-gray-700">Estado</label>
                            <select id="status" name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                                <option value="pendiente" {{ $order->status === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="facturado" {{ $order->status === 'facturado' ? 'selected' : '' }}>Facturado</option>
                            </select>
                        </div>

                        <!-- Buscador de Productos -->
                        <div class="mt-4">
                            <x-input-label for="product_search" :value="__('Buscar Productos')" />
                            <input type="text" id="product_search" name="product_search"
                                class="block mt-1 w-full rounded-md" placeholder="Buscar productos..."
                                oninput="filterProducts()" />
                            <div id="product_list" class="mt-2 border border-gray-200 rounded-md overflow-hidden"></div>
                        </div>

                        <!-- Selección de productos y cantidades -->
                        <div class="mt-4">
                            <x-input-label for="selected_products" :value="__('Productos Seleccionados')" />
                            <div id="selected_products_list" class="block mt-1 w-full rounded-md">
                                <!-- Aquí se agregarán los productos seleccionados -->

                                @foreach ($order->products as $product)
                                    <div
                                        class="product-item flex justify-between items-center mt-2 p-2 bg-gray-100 rounded-md">
                                        <span class="product-name font-medium">{{ $product->name }} -
                                            {{ $product->code }}</span>
                                        <div class="flex items-center">
                                            <label for="quantity" class="mr-2">Cantidad:</label>
                                            <input type="number" name="quantities[]"
                                                class="quantity-input block w-16 rounded-md border border-gray-300 px-2 py-1"
                                                min="1" value="{{ $product->pivot->quantity }}">
                                            <input type="hidden" name="products[]" value="{{ $product->id }}"
                                                class="product-input">
                                            <button type="button"
                                                class="remove-product ml-4 text-red-600 hover:text-red-800">Eliminar</button>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>

                        <!-- Template para agregar productos seleccionados -->
                        <template id="product-template">
                            <div class="product-item flex justify-between items-center mt-2 p-2 bg-gray-100 rounded-md">
                                <span class="product-name font-medium"></span>
                                <div class="flex items-center">
                                    <label for="quantity" class="mr-2">Cantidad:</label>
                                    <input type="number" name="quantities[]"
                                        class="quantity-input block w-16 rounded-md border border-gray-300 px-2 py-1"
                                        min="1" value="1">
                                    <input type="hidden" name="products[]" value="" class="product-input">
                                    <button type="button"
                                        class="remove-product ml-4 text-red-600 hover:text-red-800">Eliminar</button>
                                </div>
                            </div>
                        </template>

                        <!-- Botón para guardar -->
                        <div class="flex justify-end mt-4">
                            <x-primary-button>
                                {{ __('Actualizar Orden') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // Función para filtrar productos
    function filterProducts() {
        const searchInput = document.getElementById('product_search');
        const filter = searchInput.value.toLowerCase();
        const productList = document.getElementById('product_list');
        productList.innerHTML = '';

        // Aquí debes agregar lógica para mostrar productos que coincidan con el filtro
        const products = @json($products);
        products.forEach(product => {
            if (product.name.toLowerCase().includes(filter) || product.code.toLowerCase().includes(filter)) {
                const productItem = document.createElement('div');
                productItem.className = 'product-item border p-2 mt-1 cursor-pointer';
                productItem.innerText = `${product.name} - ${product.code}`;
                productItem.onclick = () => addProductToSelected(product.id, product.name);
                productList.appendChild(productItem);
            }
        });
    }

    // Función para agregar productos seleccionados
    function addProductToSelected(id, name) {
        const productTemplate = document.getElementById('product-template').content.cloneNode(true);
        const productName = productTemplate.querySelector('.product-name');
        const productInput = productTemplate.querySelector('.product-input');

        productName.innerText = name;
        productInput.value = id;

        document.getElementById('selected_products_list').appendChild(productTemplate);

        // Limpiar el campo de búsqueda
        document.getElementById('product_search').value = '';
        document.getElementById('product_list').innerHTML = '';
    }

    // Función para eliminar productos de la lista seleccionada
    document.getElementById('selected_products_list').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-product')) {
            e.target.closest('.product-item').remove();
        }
    });
</script>







{{-- <div>
    <p>{{$order}}</p>

    <p>{{$customers}}</p>
    <p>{{$products}}</p>
</div>
 --}}
