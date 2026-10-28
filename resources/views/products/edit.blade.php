<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Edit Product</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('products.update', $product) }}" class="bg-white p-6 shadow sm:rounded-lg">
                @method('PUT')
                @include('products._form')

                <div class="mt-6 flex items-center gap-3">
                    <x-primary-button>Save</x-primary-button>
                    <a href="{{ route('products.index') }}" class="text-sm text-gray-600 underline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
