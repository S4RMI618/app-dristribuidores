<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Company') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('companies.update', $company->id) }}">
                @csrf
                @method('PUT') 

                <div class="mb-4">
                    <label for="nit" class="block text-sm font-medium text-gray-700">NIT</label>
                    <input type="text" name="nit" id="nit"
                        class="form-input rounded-md shadow-sm mt-1 block w-full"
                        value="{{ old('nit', $company->nit) }}" />
                    @error('nit')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" name="name" id="name"
                        class="form-input rounded-md shadow-sm mt-1 block w-full"
                        value="{{ old('name', $company->name) }}" />
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono</label>
                    <input type="text" name="phone" id="phone"
                        class="form-input rounded-md shadow-sm mt-1 block w-full"
                        value="{{ old('phone', $company->phone) }}" />
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="address" class="block text-sm font-medium text-gray-700">Dirección</label>
                    <input type="text" name="address" id="address"
                        class="form-input rounded-md shadow-sm mt-1 block w-full"
                        value="{{ old('address', $company->address) }}" />
                    @error('address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- field for department name --}}

                <div class="mb-4">
                    <label for="department" class="block text-sm font-medium text-gray-700">Departamento</label>
                    <select name="department" id="department" class="form-select rounded-md shadow-sm mt-1 block w-full"
                        onchange="fetchMunicipalities()">
                        <option value="">Seleccione un departamento</option>
                        @foreach (json_decode(File::get(resource_path('json/departments_municipalities.json')), true) as $department => $municipalities)
                            <option value="{{ $department }}"
                                {{ old('department', $company->department) == $department ? 'selected' : '' }}>
                                {{ $department }}</option>
                        @endforeach
                        @error('department')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </select>
                    @error('department')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- field for municipality --}}

                <div class="mb-4">
                    <label for="municipality" class="block text-sm font-medium text-gray-700">Municipio</label>
                    <select name="municipality" id="municipality"
                        class="form-select rounded-md shadow-sm mt-1 block w-full">
                        <option value="">Seleccione un municipio</option>
                        @foreach (json_decode(File::get(resource_path('json/departments_municipalities.json')), true)[$company->department] ?? [] as $municipality)
                            <option value="{{ $municipality }}"
                                {{ old('municipality', $company->municipality) == $municipality ? 'selected' : '' }}>
                                {{ $municipality }}</option>
                        @endforeach
                    </select>
                    @error('municipality')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-3 rounded font-medium w-full">Update
                        Company</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function fetchMunicipalities() {
            var department = document.getElementById('department').value;
            var municipalitySelect = document.getElementById('municipality');

            // Limpia las opciones actuales
            municipalitySelect.innerHTML = '<option value="">Seleccione un municipio</option>';

            if (department) {
                fetch(`{{ route('get-municipalities') }}?department=${department}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(municipality => {
                            var option = document.createElement('option');
                            option.value = municipality;
                            option.textContent = municipality;
                            municipalitySelect.appendChild(option);
                        });
                    });
            }
        }
    </script>
</x-app-layout>
