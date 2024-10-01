<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Companies') }}
        </h2>
    </x-slot>
    <div class="flex flex-col items-center justify-center my-8">

        <a href="{{ route('companies.create') }}"
            class="bg-blue-500 text-blue px-4 py-3 rounded font-medium text-4xl"
        >Crear Nueva Empresa</a>
    
        @if (session('success'))
            <div>
                {{ session('success') }}
            </div>
        @endif
    
        <div class="bg-slate-300">
            <table class="table-auto">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>NIT</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>Departamento</th>
                        <th>Municipio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($companies as $company)
                        <tr onclick="window.location='{{ route('companies.show', $company->id) }}'">
                            <td>{{ $company->id }}</td>
                            <td>{{ $company->nit }}</td>
                            <td>{{ $company->name }}</td>
                            <td>{{ $company->phone }}</td>
                            <td>{{ $company->address }}</td>
                            <td>{{ $company->department }}</td>
                            <td>{{ $company->city }}</td>
                            <td>
                                <a href="{{ route('companies.edit', $company->id) }}">Editar</a>
                                <form action="{{ route('companies.destroy', $company->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
    </div>
    
</x-app-layout>
