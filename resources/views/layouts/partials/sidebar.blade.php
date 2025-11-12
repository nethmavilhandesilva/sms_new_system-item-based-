<aside class="sidebar shadow-xl rounded-r-3xl flex flex-col justify-between h-screen sticky top-0"
    style="background-color: #006400 !important;"> {{-- Background color applied here --}}
    <div class="list-group shadow-sm rounded-3">
          @php
            $user = Auth::user();
            $dashboardRoute = '#'; // Default link (in case no match)
            if ($user) {
                if ($user->role === 'Level2') {
                    $dashboardRoute = route('Dashboard2');
                } elseif ($user->role === 'Admin') {
                    $dashboardRoute = route('dasboard.index');
                }
            }
        @endphp

        <a href="{{ $dashboardRoute }}" class="list-group-item list-group-item-action d-flex align-items-center"
            style="background-color: transparent !important; color: white !important;">
            <span class="material-icons me-2 text-primary">dashboard</span>
            <span class="text-white">උපකරණ පුවරුව (Dashboard)</span>
        </a>
        <a href="{{ route('items.index') }}" class="list-group-item list-group-item-action d-flex align-items-center"
            style="background-color: transparent !important; color: white !important;"> {{-- Text color to white --}}
            <span class="material-icons me-2 text-success">inventory_2</span> <span class="text-white">භාණ්ඩ
                (Items)</span>
        </a>

        <a href="{{ route('customers.index') }}"
            class="list-group-item list-group-item-action d-flex align-items-center"
            style="background-color: transparent !important; color: white !important;"> {{-- Text color to white --}}
            <span class="material-icons text-primary mr-2">people</span> <span
                class="text-white">පාරිභෝගිකයින්(Customers)</span>
        </a>
        <a href="{{ route('suppliers.index') }}"
            class="list-group-item list-group-item-action d-flex align-items-center"
            style="background-color: transparent !important; color: white !important;"> {{-- Text color to white --}}
            <span class="material-icons text-blue-600 mr-3">local_shipping</span> <span class="text-white">සැපයුම්කරුවන්
                (Suppliers)</span>
        </a>
        <a href="{{ route('grn.create') }}" class="list-group-item list-group-item-action d-flex align-items-center"
            style="background-color: transparent !important; color: white !important;"> {{-- Text color to white --}}
            <span class="material-icons text-blue-600 mr-3">assignment_turned_in</span> <span
                class="text-white">GRN</span>
        </a>

      
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</aside>