<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
            <!-- INICIO DE LA SECCIÓN DE ACCIONES RÁPIDAS -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900">Acciones Rápidas</h3>
                    <div class="mt-4">
                        <!-- Botón para la Orden de Llenado (Entrada) -->
                        <a href="{{ route('movements.create') }}" class="btn btn-primary">
                           Registrar Orden de Llenado (Entrada)
                        </a>
                        
                        <!-- Botón para el Reporte de Ventas (Salida) -->
                        <a href="{{ route('movements.create_batch_salida') }}" class="btn btn-corporate ms-3">
                           Registrar Reporte de Ventas (Salida)
                        </a>
                    </div>
                </div>
            </div>
            <!-- FIN DE LA SECCIÓN DE ACCIONES RÁPIDAS -->
        </div>
    </div>
</x-app-layout>
