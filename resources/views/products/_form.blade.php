@csrf
<div class="space-y-4">
    <div>
        <x-input-label for="name" value="Name" />
        <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $product->name)" required />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="price" value="Price" />
        <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="mt-1 block w-full"
                      :value="old('price', $product->price)" required />
        <x-input-error :messages="$errors->get('price')" class="mt-2" />
    </div>
</div>
