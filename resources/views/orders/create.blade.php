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

                        <!-- Selección del Cliente -->
                        <div>
                            <x-input-label for="customer_id" :value="__('Seleccionar Cliente')" />
                            <select id="customer_id" name="customer_id" class="block mt-1 w-full rounded-md" required>
                                <option value="">{{ __('Seleccione un cliente') }}</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->full_name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
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
                            </div>
                        </div>

                        <!-- Template para agregar productos seleccionados -->
                        <template id="product-template">
                            <div class="product-item flex justify-between items-center mt-2 p-2 bg-gray-100 rounded-md">
                                <span class="product-name font-medium"></span>
                                <span class="customer-name font-medium ml-4"></span> <!-- Nueva columna para el cliente -->
                                <div class="flex items-center">
                                    <label for="quantity" class="mr-2">Cantidad:</label>
                                    <input type="number" name="quantities[]"
                                        class="quantity-input block w-16 rounded-md border border-gray-300 px-2 py-1"
                                        min="1" value="1">
                                    <input type="hidden" name="products[]" class="product-input">
                                    <input type="hidden" name="customer_ids[]" class="customer-input"> <!-- Para guardar el ID del cliente -->
                                    <button type="button"
                                        class="remove-product ml-4 text-red-600 hover:text-red-800">Eliminar</button>
                                </div>
                            </div>
                        </template>

                        <div class="mt-6">
                            <x-primary-button type="submit">{{ __('Crear Orden') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
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
                                addProductToSelection(product);
                            });
                            productList.appendChild(div);
                        });
                    });
            }
        }

        // Función para agregar productos seleccionados
        function addProductToSelection(product) {
            // Obtener el cliente seleccionado
            let customerSelect = document.getElementById('customer_id');
            let selectedCustomer = customerSelect.options[customerSelect.selectedIndex].text;

            let template = document.getElementById('product-template').content.cloneNode(true);

            // Set product name, ID y customer ID
            template.querySelector('.product-name').textContent = `${product.name} - ${product.code}`;
            template.querySelector('.customer-name').textContent = selectedCustomer; // Mostrar nombre del cliente
            template.querySelector('.product-input').value = product.id;
            template.querySelector('.customer-input').value = customerSelect.value; // Guardar el ID del cliente

            // Eliminar producto de la lista seleccionada
            template.querySelector('.remove-product').addEventListener('click', function() {
                this.closest('.product-item').remove();
            });

            // Agregar al DOM
            document.getElementById('selected_products_list').appendChild(template);

            // Limpiar el buscador
            document.getElementById('product_list').innerHTML = '';
            document.getElementById('product_search').value = '';
        }
    </script>
</x-app-layout>
