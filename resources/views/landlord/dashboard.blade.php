<x-app-layout title="Dashboard">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Landlord Dashboard
        </h2>
    </x-slot>

    <div class="py-4 min-h-screen lg:h-screen overflow-x-auto lg:overflow-hidden">
        <div class="w-full px-2 sm:px-6 lg:px-8 h-full">
            <div class="flex flex-col lg:flex-row gap-6 h-full">
                {{-- Sidebar: Overall Statistics --}}
                <div class="w-full lg:w-1/3 flex flex-col h-[93%] min-h-[400px] order-1">
                    <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col flex-1 justify-between h-full">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700">Overall Statistics</h3>
                            <p class="text-gray-500 text-xs mb-4">
                                {{ \Carbon\Carbon::now()->format('D, d M, Y, h.i A') }}
                            </p>
                        </div>
                        <div class="flex flex-col flex-1 justify-center divide-y divide-gray-200">
                            <div class="bg-white p-4 flex flex-col items-center flex-1 justify-center">
                                <span class="text-sm text-gray-500 font-medium">Total Properties</span>
                                <span class="text-2xl font-bold text-gray-800 mt-2">{{ $totalProperties ?? 0 }}</span>
                            </div>
                            <div class="bg-white p-4 flex flex-col items-center flex-1 justify-center">
                                <span class="text-sm text-gray-500 font-medium">Total Occupants</span>
                                <span class="text-2xl font-bold text-gray-800 mt-2">{{ $totalOccupants ?? 0 }}</span>
                            </div>
                            <div class="bg-white p-4 flex flex-col items-center flex-1 justify-center">
                                <span class="text-sm text-gray-500 font-medium">Total Monthly Rent Due</span>
                                <span class="text-2xl font-bold text-gray-800 mt-2">
                                    {{ number_format($totalMonthlyRentDue ?? 0, 2) . ' PHP' }}
                                </span>
                            </div>
                            <div class="bg-white p-4 flex flex-col items-center flex-1 justify-center">
                                <span class="text-sm text-gray-500 font-medium">Total Outstanding Rent</span>
                                <span class="text-2xl font-bold text-gray-800 mt-2">
                                    {{ number_format($totalOutstandingRent ?? 0, 2) . ' PHP' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Main Content Column --}}
                <div class="w-full lg:w-2/3 flex flex-col h-[93%] min-h-[400px] order-2">
                    <div class="flex flex-col flex-1 gap-4 h-full">
                        <!-- Quick Access Buttons -->
                        <div class="bg-white rounded-xl shadow-lg p-6 mb-4">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Quick Access Buttons</h3>
                            <div class="flex flex-col md:flex-row gap-4 md:gap-6 md:justify-between md:items-center">
                                <a href="{{ route('property.create') }}"
                                   class="w-full md:w-1/3 h-16 py-4 text-xs inline-flex items-center justify-center
                                          bg-lime-600 border border-transparent text-white rounded-md
                                          font-bold text-center uppercase tracking-widest
                                          transition ease-in-out duration-150
                                          hover:bg-lime-700 focus:bg-lime-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500
                                          active:bg-lime-800 cursor-pointer px-8 shadow-lg">
                                    Add Properties
                                </a>
                                <a href="{{ route('announcement.create') }}"
                                   class="w-full md:w-1/3 h-16 py-4 text-xs inline-flex items-center justify-center
                                          bg-lime-600 border border-transparent text-white rounded-md
                                          font-bold text-center uppercase tracking-widest
                                          transition ease-in-out duration-150
                                          hover:bg-lime-700 focus:bg-lime-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500
                                          active:bg-lime-800 cursor-pointer px-8 shadow-lg">
                                    Create Announcement
                                </a>
                                <a href="{{ route('property.index') }}"
                                   class="w-full md:w-1/3 h-16 py-4 text-xs inline-flex items-center justify-center
                                          bg-lime-600 border border-transparent text-white rounded-md
                                          font-bold text-center uppercase tracking-widest
                                          transition ease-in-out duration-150
                                          hover:bg-lime-700 focus:bg-lime-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500
                                          active:bg-lime-800 cursor-pointer px-8 shadow-lg">
                                    Manage Properties
                                </a>
                            </div>
                        </div>

                        <!-- Announcements & Maintenance Requests (stretch to fill) -->
                        <div class="flex flex-col flex-1 gap-4 h-full min-h-0">
                            <!-- Announcements -->
                            <div class="bg-white rounded-xl shadow-lg p-6 flex-1 flex flex-col overflow-auto min-h-0">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-semibold text-gray-700">Announcements</h3>
                                    <a href="{{ route('announcement.index') }}"
                                       class="inline-flex items-center justify-center px-4 py-2 bg-lime-600 text-white rounded-md
                                       font-semibold text-xs uppercase shadow-sm
                                       hover:bg-lime-700 focus:outline-none focus:ring-2 focus:ring-lime-500 focus:ring-offset-2
                                       transition ease-in-out duration-150">
                                        See All
                                    </a>
                                </div>
                                @if ($landlordAnnouncements->isEmpty())
                                    <p class="text-gray-500 text-center">No announcements to display.</p>
                                @else
                                    <div class="overflow-x-auto w-full">
                                        <table class="min-w-full divide-y divide-gray-200 table-fixed">
                                            <thead>
                                                <tr>
                                                    <th class="px-4 py-2 w-[8%] whitespace-nowrap text-gray-600 text-center bg-lime-700 text-white font-semibold rounded-tl-lg">No.</th>
                                                    <th class="px-4 py-2 w-[18%] whitespace-nowrap text-gray-600 text-center bg-lime-700 text-white font-semibold">Property</th>
                                                    <th class="px-4 py-2 w-[17%] whitespace-nowrap text-gray-600 text-center bg-lime-700 text-white font-semibold">Room</th>
                                                    <th class="px-4 py-2 w-[35%] whitespace-nowrap text-gray-600 text-left bg-lime-700 text-white font-semibold">Title</th>
                                                    <th class="px-4 py-2 w-[17%] whitespace-nowrap text-gray-600 text-center bg-lime-700 text-white font-semibold">Date Issued</th>
                                                    <th class="px-4 py-2 w-[10%] whitespace-nowrap text-gray-600 text-center bg-lime-700 text-white font-semibold rounded-tr-lg">Details</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach ($landlordAnnouncements as $index => $announcement)
                                                    <tr>
                                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-center align-middle text-gray-900">
                                                            <span class="inline-block px-2 py-1 bg-gray-100 text-gray-800 rounded-md text-xs font-semibold">
                                                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-center align-middle text-gray-900">
                                                            @if ($announcement->property)
                                                                {{ $announcement->property->name }}
                                                            @elseif ($announcement->room && $announcement->room->property)
                                                                {{ $announcement->room->property->name }}
                                                            @else
                                                                System Wide
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-center align-middle text-gray-900">
                                                            {{ $announcement->room->code ?? '—' }}
                                                        </td>
                                                        <td class="px-4 py-2 text-sm align-middle text-gray-900 font-bold overflow-hidden text-ellipsis whitespace-nowrap text-left">
                                                            {{ $announcement->title }}
                                                        </td>
                                                        <td class="px-4 py-2 whitespace-nowrap text-sm align-middle text-gray-500 text-center">
                                                            {{ $announcement->created_at->format('d M, Y') }}
                                                        </td>
                                                        <td class="px-4 py-2 whitespace-nowrap text-center align-middle text-sm font-medium">
                                                            <a href="{{ route('announcement.show', $announcement->id) }}"
                                                               class="inline-flex items-center justify-center px-4 py-2 bg-lime-600 text-white rounded-md
                                                                      font-semibold text-xs uppercase shadow-sm
                                                                      hover:bg-lime-700 focus:outline-none focus:ring-2 focus:ring-lime-500 focus:ring-offset-2
                                                                      transition ease-in-out duration-150">
                                                                Details
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                            <!-- Maintenance Requests -->
                            <div class="bg-white rounded-xl shadow-lg p-6 flex-1 flex flex-col overflow-auto min-h-0">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-semibold text-gray-700">Maintenance Requests</h3>
                                    <a href="{{ route('request.index') }}"
                                       class="inline-flex items-center justify-center px-4 py-2 bg-lime-600 text-white rounded-md
                                       font-semibold text-xs uppercase shadow-sm
                                       hover:bg-lime-700 focus:outline-none focus:ring-2 focus:ring-lime-500 focus:ring-offset-2
                                       transition ease-in-out duration-150">
                                        See All
                                    </a>
                                </div>
                                @if ($landlordMaintenanceRequests->isEmpty())
                                    <p class="text-gray-500 text-center">No pending maintenance requests.</p>
                                @else
                                    <div class="overflow-x-auto w-full">
                                        <table class="min-w-full divide-y divide-gray-200 table-fixed">
                                            <thead>
                                                <tr>
                                                    <th class="px-4 py-2 w-[8%] whitespace-nowrap text-gray-600 text-center bg-lime-700 text-white font-semibold rounded-tl-lg">No.</th>
                                                    <th class="px-4 py-2 w-[20%] whitespace-nowrap text-gray-600 text-center bg-lime-700 text-white font-semibold">Property</th>
                                                    <th class="px-4 py-2 w-[17%] whitespace-nowrap text-gray-600 text-center bg-lime-700 text-white font-semibold">Room</th>
                                                    <th class="px-4 py-2 w-[20%] whitespace-nowrap text-gray-600 text-center bg-lime-700 text-white font-semibold">Tenant</th>
                                                    <th class="px-4 py-2 w-[25%] whitespace-nowrap text-gray-600 text-left bg-lime-700 text-white font-semibold">Title</th>
                                                    <th class="px-4 py-2 w-[10%] whitespace-nowrap text-gray-600 text-center bg-lime-700 text-white font-semibold rounded-tr-lg">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach ($landlordMaintenanceRequests as $index => $request)
                                                    <tr>
                                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-center align-middle text-gray-900">
                                                            <span class="inline-block px-2 py-1 bg-gray-100 text-gray-800 rounded-md text-xs font-semibold">
                                                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-center align-middle text-gray-900">
                                                            {{ $request->room->property->name ?? 'N/A' }}
                                                        </td>
                                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-center align-middle text-gray-900">
                                                            {{ $request->room->code ?? 'N/A' }}
                                                        </td>
                                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-center align-middle text-gray-900">
                                                            {{ $request->tenant->user->name ?? 'N/A' }}
                                                        </td>
                                                        <td class="px-4 py-2 text-sm align-middle text-gray-900 font-bold overflow-hidden text-ellipsis whitespace-nowrap text-left">
                                                            {{ $request->title }}
                                                        </td>
                                                        <td class="px-4 py-2 whitespace-nowrap text-sm align-middle text-gray-900 text-center">
                                                            @php
                                                                $statusClass = '';
                                                                switch ($request->status) {
                                                                    case 'resolved':
                                                                        $statusClass = 'bg-green-500';
                                                                        break;
                                                                    case 'pending':
                                                                        $statusClass = 'bg-yellow-500';
                                                                        break;
                                                                    case 'in_progress':
                                                                        $statusClass = 'bg-lime-500';
                                                                        break;
                                                                    case 'rejected':
                                                                        $statusClass = 'bg-red-500';
                                                                        break;
                                                                    default:
                                                                        $statusClass = 'bg-gray-400';
                                                                        break;
                                                                }
                                                            @endphp
                                                            <div class="flex items-center gap-2 justify-center">
                                                                <span class="w-3 h-3 rounded-full {{ $statusClass }}"></span>
                                                                <span class="capitalize text-sm">{{ str_replace('_', ' ', $request->status) }}</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div><!-- end flex-1 -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
