<x-app-layout>
    <div class="p-6">

        <h1 class="text-3xl font-bold mb-6">
            EventSphere Dashboard
        </h1>

        <div class="grid grid-cols-2 gap-6">

            <div class="bg-white p-4 rounded shadow">
                <h2 class="text-xl font-semibold mb-3">
                    Recent Clubs
                </h2>

                @forelse($clubs as $club)
                    <p>{{ $club->name }}</p>
                @empty
                    <p>No clubs found.</p>
                @endforelse
            </div>

            <div class="bg-white p-4 rounded shadow">
                <h2 class="text-xl font-semibold mb-3">
                    Recent Events
                </h2>

                @forelse($events as $event)
                    <p>{{ $event->title }}</p>
                @empty
                    <p>No events found.</p>
                @endforelse
            </div>

        </div>

    </div>
</x-app-layout>