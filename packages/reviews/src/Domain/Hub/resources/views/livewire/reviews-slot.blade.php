<div class="shadow sm:rounded-md">
    <div class="flex-col px-4 py-5 space-y-4 bg-white rounded-md sm:p-6">
        <header>
            <h3 class="text-lg font-medium leading-6 text-gray-900">
                Reviews
            </h3>
        </header>
        <div class="space-y-4">
            <div class="flex items-center flex-wrap space-x-4">
                <table class="table-auto w-full">
                    <thead>
                        <tr class="text-left">
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Published</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reviews as $review)
                            <tr>
                                <td>{{ $review->rating }}</td>
                                <td>{{ $review->comment }}</td>
                                <td>{{ $review->published_at }}</td>
                                <td>
                                    <button wire:click.prevent="toggle({{ $review->id }})"
                                            class="px-2 py-1 text-xs text-green-600 border border-green-500 rounded hover:bg-green-50">
                                        {{ $review->published_at ? 'Unpublish' : 'Publish' }}
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="grow mt-8">
                    {{ $reviews->links() }}
                </div>
            </div>
        </div>
    </div>
</div>