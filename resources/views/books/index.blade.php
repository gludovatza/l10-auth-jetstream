<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Books') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    @can('create', \App\Models\Book::class)
                        <x-nav-link href="{{ route('books.create') }}" class="m-4">Add new book</x-nav-link>
                    @endcan
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Title
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Actions
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($books as $book)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                    <x-nav-link href="{{ route('books.show', $book) }}">{{ $book->title }}</x-nav-link>
                                </td>
                                <td class="px-6 py-4">
                                    @can('update', $book)
                                    <x-nav-link href="{{ route('books.edit', $book) }}">Edit</x-nav-link>
                                    @endcan

                                    @can('delete', $book)
                                    <form method="POST" action="{{ route('books.destroy', $book) }}" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <x-danger-button
                                            type="submit"
                                            onclick="return confirm('Are you sure?')">Delete</x-danger-button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td colspan="2"
                                    class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                    {{ __('No books found') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
