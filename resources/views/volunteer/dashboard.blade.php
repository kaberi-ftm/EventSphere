<x-app-layout>

    <x-slot name="header">
        <h2 style="font-size: 22px; font-weight: 700; color: #111827; margin: 0;">
            Volunteer Dashboard
        </h2>
    </x-slot>

    <div style="
        min-height: calc(100vh - 130px);
        background: #f3f4f6;
        padding: 30px;
    ">

        <div style="max-width: 1400px; margin: 0 auto;">

            {{-- Welcome --}}
            <div style="margin-bottom: 25px;">
                <h1 style="
                    color: #111827;
                    font-size: 28px;
                    font-weight: 700;
                    margin-bottom: 5px;
                ">
                    Welcome, {{ auth()->user()->name }}
                </h1>

                <p style="color: #6b7280; margin: 0;">
                    View your volunteer applications and assigned tasks.
                </p>
            </div>

            {{-- Messages --}}
            @if(session('success'))
                <div style="
                    background: #d1fae5;
                    color: #065f46;
                    padding: 14px 18px;
                    border-radius: 8px;
                    margin-bottom: 20px;
                ">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="
                    background: #fee2e2;
                    color: #991b1b;
                    padding: 14px 18px;
                    border-radius: 8px;
                    margin-bottom: 20px;
                ">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Statistics --}}
            <div style="
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 20px;
                margin-bottom: 30px;
            ">

                <div style="
                    background: white;
                    padding: 22px;
                    border-radius: 12px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
                ">
                    <div style="
                        font-size: 32px;
                        font-weight: 700;
                        color: #d97706;
                    ">
                        {{ $pendingTasks ?? 0 }}
                    </div>

                    <div style="color: #6b7280;">
                        Pending Tasks
                    </div>
                </div>

                <div style="
                    background: white;
                    padding: 22px;
                    border-radius: 12px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
                ">
                    <div style="
                        font-size: 32px;
                        font-weight: 700;
                        color: #2563eb;
                    ">
                        {{ $inProgressTasks ?? 0 }}
                    </div>

                    <div style="color: #6b7280;">
                        In Progress
                    </div>
                </div>

                <div style="
                    background: white;
                    padding: 22px;
                    border-radius: 12px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
                ">
                    <div style="
                        font-size: 32px;
                        font-weight: 700;
                        color: #059669;
                    ">
                        {{ $completedTasks ?? 0 }}
                    </div>

                    <div style="color: #6b7280;">
                        Completed Tasks
                    </div>
                </div>

            </div>

            {{-- Applications --}}
            <div style="
                background: white;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.06);
                margin-bottom: 30px;
                overflow: hidden;
            ">

                <div style="
                    padding: 18px 22px;
                    border-bottom: 1px solid #e5e7eb;
                ">
                    <h2 style="
                        margin: 0;
                        color: #111827;
                        font-size: 20px;
                        font-weight: 700;
                    ">
                        My Volunteer Applications
                    </h2>
                </div>

                <div style="overflow-x: auto;">
                    <table style="
                        width: 100%;
                        border-collapse: collapse;
                        color: #374151;
                    ">

                        <thead>
                            <tr style="background: #f9fafb;">
                                <th style="padding: 14px; text-align: left;">
                                    Event
                                </th>

                                <th style="padding: 14px; text-align: left;">
                                    Role
                                </th>

                                <th style="padding: 14px; text-align: left;">
                                    Start Time
                                </th>

                                <th style="padding: 14px; text-align: left;">
                                    Applied At
                                </th>

                                <th style="padding: 14px; text-align: left;">
                                    Status
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($applications as $application)

                                @php
                                    $applicationStatus =
                                        strtolower((string) $application->status);
                                @endphp

                                <tr style="border-top: 1px solid #e5e7eb;">
                                    <td style="padding: 14px;">
                                        {{ $application->event_title }}
                                    </td>

                                    <td style="padding: 14px;">
                                        {{ $application->role }}
                                    </td>

                                    <td style="padding: 14px;">
                                        {{ $application->start_time ?? 'N/A' }}
                                    </td>

                                    <td style="padding: 14px;">
                                        {{ $application->applied_at ?? 'N/A' }}
                                    </td>

                                    <td style="padding: 14px;">
                                        <span style="
                                            display: inline-block;
                                            padding: 5px 12px;
                                            border-radius: 20px;
                                            font-size: 13px;
                                            font-weight: 600;

                                            @if($applicationStatus === 'approved')
                                                background: #d1fae5;
                                                color: #065f46;
                                            @elseif($applicationStatus === 'rejected')
                                                background: #fee2e2;
                                                color: #991b1b;
                                            @else
                                                background: #fef3c7;
                                                color: #92400e;
                                            @endif
                                        ">
                                            {{ ucfirst($applicationStatus) }}
                                        </span>
                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5"
                                        style="
                                            padding: 35px;
                                            text-align: center;
                                            color: #6b7280;
                                        ">
                                        No volunteer applications found.
                                    </td>
                                </tr>

                            @endforelse
                        </tbody>

                    </table>
                </div>
            </div>

            {{-- Tasks --}}
            <div style="
                background: white;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.06);
                overflow: hidden;
            ">

                <div style="
                    padding: 18px 22px;
                    border-bottom: 1px solid #e5e7eb;
                ">
                    <h2 style="
                        margin: 0;
                        color: #111827;
                        font-size: 20px;
                        font-weight: 700;
                    ">
                        My Assigned Tasks
                    </h2>
                </div>

                <div style="overflow-x: auto;">
                    <table style="
                        width: 100%;
                        border-collapse: collapse;
                        color: #374151;
                    ">

                        <thead>
                            <tr style="background: #f9fafb;">
                                <th style="padding: 14px; text-align: left;">
                                    Event
                                </th>

                                <th style="padding: 14px; text-align: left;">
                                    Task
                                </th>

                                <th style="padding: 14px; text-align: left;">
                                    Description
                                </th>

                                <th style="padding: 14px; text-align: left;">
                                    Deadline
                                </th>

                                <th style="padding: 14px; text-align: left;">
                                    Status
                                </th>

                                <th style="padding: 14px; text-align: left;">
                                    Action
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($tasks as $task)

                                @php
                                    $taskStatus =
                                        strtolower((string) $task->status);
                                @endphp

                                <tr style="border-top: 1px solid #e5e7eb;">
                                    <td style="padding: 14px;">
                                        {{ $task->event_title }}
                                    </td>

                                    <td style="padding: 14px;">
                                        <strong>
                                            {{ $task->title }}
                                        </strong>
                                    </td>

                                    <td style="padding: 14px;">
                                        {{ $task->description ?? 'No description' }}
                                    </td>

                                    <td style="padding: 14px;">
                                        {{ $task->deadline ?? 'No deadline' }}
                                    </td>

                                    <td style="padding: 14px;">
                                        <span style="
                                            display: inline-block;
                                            padding: 5px 12px;
                                            border-radius: 20px;
                                            font-size: 13px;
                                            font-weight: 600;

                                            @if($taskStatus === 'completed')
                                                background: #d1fae5;
                                                color: #065f46;
                                            @elseif($taskStatus === 'in_progress')
                                                background: #dbeafe;
                                                color: #1e40af;
                                            @else
                                                background: #fef3c7;
                                                color: #92400e;
                                            @endif
                                        ">
                                            {{ ucwords(str_replace('_', ' ', $taskStatus)) }}
                                        </span>
                                    </td>

                                    <td style="padding: 14px;">

                                        @if($taskStatus === 'pending')

                                            <form method="POST"
                                                  action="{{ route(
                                                      'volunteer.tasks.start',
                                                      $task->id
                                                  ) }}">
                                                @csrf

                                                <button type="submit"
                                                        style="
                                                            border: 0;
                                                            background: #2563eb;
                                                            color: white;
                                                            padding: 8px 14px;
                                                            border-radius: 6px;
                                                            cursor: pointer;
                                                        ">
                                                    Start Task
                                                </button>
                                            </form>

                                        @elseif($taskStatus === 'in_progress')

                                            <form method="POST"
                                                  action="{{ route(
                                                      'volunteer.tasks.complete',
                                                      $task->id
                                                  ) }}">
                                                @csrf

                                                <button type="submit"
                                                        style="
                                                            border: 0;
                                                            background: #059669;
                                                            color: white;
                                                            padding: 8px 14px;
                                                            border-radius: 6px;
                                                            cursor: pointer;
                                                        ">
                                                    Complete Task
                                                </button>
                                            </form>

                                        @else

                                            <span style="
                                                color: #059669;
                                                font-weight: 700;
                                            ">
                                                Finished
                                            </span>

                                        @endif

                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="6"
                                        style="
                                            padding: 35px;
                                            text-align: center;
                                            color: #6b7280;
                                        ">
                                        No tasks have been assigned to you.
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