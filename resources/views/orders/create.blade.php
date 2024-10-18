<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Orden') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="p-4 py-5 sm:px-6">
                    <form action="{{ route('orders.store') }}" method="POST">
                        @csrf

                        <!-- Buscador interactivo del Cliente -->
                        <div>
                            <x-input-label for="customer_search" :value="__('Buscar Cliente')" />
                            <input type="text" id="customer_search" name="customer_search"
                                class="block mt-1 w-full rounded-md" placeholder="Buscar por nombre o identificación"
                                oninput="filterCustomers()" />
                            <div id="customer_list" class="mt-2 border border-gray-200 rounded-md overflow-hidden">
                            </div>
                            <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
                        </div>

                        <!-- Selección del Cliente -->
                        <input type="hidden" id="customer_id" name="customer_id">

                        <!-- Buscador de Productos -->
                        <div class="mt-4">
                            <x-input-label for="product_search" :value="__('Buscar Productos')" />
                            <input type="text" id="product_search" name="product_search"
                                class="block mt-1 w-full rounded-md" placeholder="Buscar productos..."
                                oninput="filterProducts()" />
                            <div id="product_list" class="mt-2 border border-gray-200 rounded-md overflow-hidden"></div>
                        </div>

                        <!-- Detalles del Producto Seleccionado -->
                        <div id="selected_product_details" class="mt-4 hidden">
                            <h3 class="text-lg font-semibold">Detalles del Producto</h3>
                            <div class="p-4 bg-gray-100 rounded-md">
                                <p id="product_name" class="font-medium"></p>
                                <p id="product_code"></p>
                                <p id="product_price"></p>
                                <button type="button" id="add_product_button"
                                    class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-md">Agregar Producto</button>
                            </div>
                        </div>

                        <!-- Selección de productos y cantidades -->
                        <div class="mt-4">
                            <x-input-label for="selected_products" :value="__('Productos Seleccionados')" />
                            <div id="selected_products_list" class="block mt-1 w-full rounded-md">
                                <!-- Aquí se agregarán los productos seleccionados -->
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
                                    <input type="hidden" name="products[]" class="product-input">
                                    <input type="hidden" name="product_prices[]" class="product-price-input">
                                    <button type="button"
                                        class="remove-product ml-4 text-red-600 hover:text-red-800">Eliminar</button>
                                </div>
                            </div>
                        </template>

                        <!-- Resumen del total de la orden -->
                        <div id="order_totals" class="mt-6 p-4 bg-gray-100 rounded-md hidden">
                            <h3 class="text-lg font-semibold">Totales de la Orden</h3>
                            <div class="mt-2">
                                <p id="total_base_price">Total Precio Base: $0.00</p>
                                <p id="total_tax">Total Impuestos: $0.00</p>
                                <p id="total_price">Total Final: $0.00</p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <x-primary-button type="submit">{{ __('Crear Orden') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Función para filtrar clientes por nombre o identificación
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
            document.getElementById('customer_search').value = `${customer.full_name} - ${customer.identification}`;
            document.getElementById('customer_id').value = customer.id;
            document.getElementById('customer_list').innerHTML = ''; // Limpiar lista
        }

        let selectedProduct = null; // Variable para almacenar el producto seleccionado

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

        // Función para mostrar los detalles del producto seleccionado
        function showProductDetails(product) {
            selectedProduct = product;
            const basePrice = parseFloat(product.base_price);
            const taxRate = parseFloat(product.tax_rate);

            document.getElementById('product_name').textContent = `Nombre: ${product.name}`;
            document.getElementById('product_code').textContent = `Código: ${product.code}`;
            document.getElementById('product_price').textContent =
                `Precio: $${(basePrice + (basePrice * taxRate) / 100).toFixed(2)}`;

            document.getElementById('selected_product_details').classList.remove('hidden');
        }

        // Función para agregar productos seleccionados
        document.getElementById('add_product_button').addEventListener('click', function() {
            if (!selectedProduct) return;

            let template = document.getElementById('product-template').content.cloneNode(true);

            // Set product name, ID, and price
            template.querySelector('.product-name').textContent =
                `${selectedProduct.name} - ${selectedProduct.code}`;
            template.querySelector('.product-input').value = selectedProduct.id;
            template.querySelector('.product-price-input').value = selectedProduct.price;

            // Eliminar producto de la lista seleccionada
            template.querySelector('.remove-product').addEventListener('click', function() {
                this.closest('.product-item').remove();
            });

            // Agregar al DOM
            document.getElementById('selected_products_list').appendChild(template);

            // Limpiar los detalles del producto
            document.getElementById('selected_product_details').classList.add('hidden');
            selectedProduct = null;
        });

        
    </script>
</x-app-layout>
