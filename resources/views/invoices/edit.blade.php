<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Edit Invoice</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form method="PUT" action="{{ route('invoices.update') }}" class="bg-white p-6 shadow sm:rounded-lg">
                @include('invoices._form')

                <div class="mt-6 flex items-center gap-3">
                    <x-primary-button>Save draft</x-primary-button>
                    <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 underline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
