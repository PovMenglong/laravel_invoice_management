<form method="POST" action="{{ route('customers.update', $customer) }}" class="bg-white p-6 shadow sm:rounded-lg">
    @method('PUT')
    @include('customers._form')
    ...
