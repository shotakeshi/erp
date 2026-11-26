@props([
    'assignments',
    'langPrefix',
])

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered mb-0 table-centered">
                <thead>
                    <tr>
                        <th>{{ __($langPrefix.'.employee') }}</th>
                        <th>{{ __($langPrefix.'.role') }}</th>
                        <th>{{ __($langPrefix.'.assignment_period') }}</th>
                        <th>{{ __($langPrefix.'.end_reason') }}</th>
                        <th>{{ __($langPrefix.'.created_by') }}</th>
                        <th>{{ __($langPrefix.'.ended_by') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assignments as $assignment)
                        @php
                            $employee = $assignment->employee;
                        @endphp
                        <tr>
                            <td>
                                <div class="media">
                                    @if ($employee->trashed())
                                        <img src="{{ image_url($employee->avatar) }}" alt="{{ $employee->full_name }}" class="rounded-circle thumb-md">
                                    @else
                                        <a class="" href="{{ route('employees.show', $employee) }}">
                                            <img src="{{ image_url($employee->avatar) }}" alt="{{ $employee->full_name }}" class="rounded-circle thumb-md">
                                        </a>
                                    @endif
                                    <div class="media-body align-self-center ml-3">
                                        <p class="font-14 font-weight-bold mb-0">
                                            {{ $employee->full_name }}
                                            @if ($employee->trashed())
                                                <span class="badge badge-soft-danger ml-1">{{ __($langPrefix.'.employee_deleted') }}</span>
                                            @endif
                                        </p>
                                        <p class="mb-0 font-12 text-muted">{{ $employee->email }}</p>
                                    </div>
                                </div><!--end media-->
                            </td>
                            <td>{{ $assignment->role }}</td>
                            <td class="text-nowrap">
                                {{ $assignment->start_date->format('d/m/Y') }}
                                <i class="fas fa-long-arrow-alt-right"></i>
                                @if ($assignment->end_date)
                                    {{ $assignment->end_date->format('d/m/Y') }}
                                @else
                                    <span class="badge badge-soft-success">{{ __($langPrefix.'.history_filters.current') }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $assignment->end_reason?->label() ?? '-' }}
                                @if (filled($assignment->end_reason_note))
                                    <small class="d-block text-muted mt-1">{{ $assignment->end_reason_note }}</small>
                                @endif
                            </td>
                            <td>{{ $assignment->createdBy?->name ?? __($langPrefix.'.system_or_legacy') }}</td>
                            <td>{{ $assignment->endedBy?->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-history d-block font-20 mb-2"></i>
                                {{ __($langPrefix.'.no_member_history') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :paginator="$assignments" />
    </div>
</div>
