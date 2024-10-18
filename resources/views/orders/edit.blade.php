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
                        <div class="mt-4">
                            <x-input-label for="customer_search" :value="__('Cliente')" />
                            <input type="text" id="customer_search" name="customer_search"
                                class="block mt-1 w-full rounded-md"
                                placeholder="Escriba el nombre o identificación del cliente..."
                                value="{{ $order->customer->full_name }} - {{ $order->customer->identification }}"
                                oninput="filterCustomers()" />
                            <div id="customer_list" class="mt-2 border border-gray-200 rounded-md overflow-hidden">
                            </div>
                        </div>

                        <!-- Campo oculto para almacenar el cliente seleccionado -->
                        <input type="hidden" id="customer_id" name="customer_id" value="{{ $order->customer_id }}">

                        <!-- Estado de la Orden -->
                        <div class="mt-4">
                            <x-input-label for="status" :value="__('Estado')" />
                            <select id="status" name="status" class="block mt-1 w-full rounded-md p-2" required>
                                <option value="pendiente" {{ $order->status === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="facturado" {{ $order->status === 'facturado' ? 'selected' : '' }}>Facturado</option>
                            </select>
                        </div>

                        <!-- Buscador de Productos -->
                        <div class="mt-4">
                            <x-input-label for="product_search" :value="__('Buscar Productos')" />
                            <input type="text" id="product_search" name="product_search"
                                class="block mt-1 w-full rounded-md" placeholder="Escriba el producto..."
                                oninput="filterProducts()" />
                            <div id="product_list" class="mt-2 border border-gray-200 rounded-md overflow-hidden"></div>
                        </div>

                        <!-- Detalles del producto seleccionado -->
                        <div id="product_details" class="mt-4 hidden">
                            <h3 class="text-lg font-medium">{{ __('Agregar Producto') }}</h3>
                            <div class="mt-2 p-4 border border-gray-200 rounded-md bg-gray-50">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-medium" id="selected_product_name"></p>
                                        <p>Precio: $<span id="selected_product_price"></span></p>
                                        <p>Impuestos: $<span id="selected_product_tax"></span></p>
                                        <p>Total: $<span id="selected_product_total"></span></p>
                                    </div>
                                    <div>
                                        <label for="selected_product_quantity" class="block">Cantidad:</label>
                                        <input type="number" id="selected_product_quantity" name="quantity"
                                            class="block w-16 rounded-md" min="1" value="1"
                                            oninput="updateTotal()">
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <button type="button" onclick="addProductToOrder()"
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        {{ __('Agregar Producto') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Lista de productos seleccionados -->
                        <div class="mt-4">
                            <x-input-label for="selected_products" :value="__('Productos Seleccionados')" />
                            <div id="selected_products_list"
                                class="mt-2 border border-gray-200 rounded-md overflow-hidden">
                                @foreach ($order->products as $product)
                                    <div class="product-item flex justify-between items-center mt-2 p-2 bg-gray-100 rounded-md">
                                        <span class="product-name font-medium">{{ $product->name }} - {{ $product->code }}</span>
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

                        <div class="mt-6">
                            <x-primary-button type="submit">{{ __('Actualizar Orden') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Template para productos seleccionados -->
    <template id="product-template">
        <div class="product-item flex justify-between items-center mt-2 p-2 bg-gray-100 rounded-md">
            <span class="product-name font-medium"></span>
            <div class="flex items-center">
                <label for="quantity" class="mr-2">Cantidad:</label>
                <input type="number" name="quantities[]"
                    class="quantity-input block w-16 rounded-md border border-gray-300 px-2 py-1" min="1"
                    value="1">
                <input type="hidden" name="products[]" class="product-input">
                <button type="button" class="remove-product ml-4 text-red-600 hover:text-red-800">Eliminar</button>
            </div>
        </div>
    </template>
</x-app-layout>

<script>
    let selectedProduct = null;

    // Función para filtrar clientes
    function filterCustomers() {
        let query = document.getElementById('customer_search').value;

        if (query.length > 1) {
            fetch(`/customers/search?query=${query}`)
                .then(response => response.json())
                .then(data => {
                    let customerList = document.getElementById('customer_list');
                    customerList.innerHTML = ''; // Limpiar lista anterior

                    data.forEach(customer => {
                        let div = document.createElement('div');
                        div.textContent = `${customer.full_name} - ${customer.identification}`;
                        div.classList.add('p-2', 'cursor-pointer', 'hover:bg-gray-200');
                        div.addEventListener('click', function() {
                            selectCustomer(customer);
                        });
                        customerList.appendChild(div);
                    });
                });
        }
    }

    // Función para seleccionar cliente
    function selectCustomer(customer) {
        document.getElementById('customer_id').value = customer.id;
        document.getElementById('customer_search').value = `${customer.full_name} - ${customer.identification}`;
        document.getElementById('customer_list').innerHTML = '';
    }

    // Función para filtrar productos
    function filterProducts() {
        let query = document.getElementById('product_search').value;

        if (query.length > 1) {
            fetch(`/products/search?query=${query}`)
                .then(response => response.json())
                .then(data => {
                    let productList = document.getElementById('product_list');
                    productList.innerHTML = ''; // Limpiar lista anterior

                    data.forEach(product => {
                        let div = document.createElement('div');
                        div.textContent = `${product.name} - ${product.code}`;
                        div.classList.add('p-2', 'cursor-pointer', 'hover:bg-gray-200');
                        div.addEventListener('click', function() {
                            showProductDetails(product);
                        });
                        productList.appendChild(div);
                    });
                });
        }
    }

    // Mostrar los detalles del producto seleccionado
    function showProductDetails(product) {
        selectedProduct = product;
        document.getElementById('selected_product_name').textContent = `${product.name} - ${product.code}`;
        document.getElementById('selected_product_price').textContent = product.base_price;
        document.getElementById('selected_product_tax').textContent = product.tax_rate;
        updateTotal();
        document.getElementById('product_details').classList.remove('hidden');
    }

    // Actualizar el total al cambiar la cantidad
    function updateTotal() {
        let quantity = document.getElementById('selected_product_quantity').value;
        let total = (parseFloat(selectedProduct.base_price) + parseFloat(selectedProduct.tax_rate)) * quantity;
        document.getElementById('selected_product_total').textContent = total.toFixed(2);
    }

    // Agregar producto a la lista de productos seleccionados
    function addProductToOrder() {
        if (!selectedProduct) {
            console.error('No hay un producto seleccionado');
            return;
        }

        let quantity = document.getElementById('selected_product_quantity').value;
        let selectedProductsList = document.getElementById('selected_products_list');

        let template = document.getElementById('product-template');
        let newProductItem = document.importNode(template.content, true);

        newProductItem.querySelector('.product-name').textContent = `${selectedProduct.name} - ${selectedProduct.code}`;
        newProductItem.querySelector('.product-input').value = selectedProduct.id;
        newProductItem.querySelector('.quantity-input').value = quantity;

        newProductItem.querySelector('.remove-product').addEventListener('click', function() {
            this.closest('.product-item').remove();
        });

        selectedProductsList.appendChild(newProductItem);

        // Limpiar y resetear
        document.getElementById('product_list').innerHTML = '';
        document.getElementById('product_search').value = '';
        document.getElementById('product_details').classList.add('hidden');
        selectedProduct = null;
    }

    // Agregar event listener para eliminar productos
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-product')) {
            e.target.closest('.product-item').remove();
        }
    });
</script>