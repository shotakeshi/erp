<div class="table-responsive mt-3">
    <table class="table mb-0 table-centered">
        <tbody>
            @foreach($departments as $department)
                <tr>
                    <td class="pl-0">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-box thumb-sm align-self-center mr-2">
                                <span class="avatar-title bg-purple rounded"><i class="fas fa-building"></i></span>
                            </div>
                            <div>
                                <div class="font-14 mb-1">
                                    {{ $department->name }}{{ $department->parent ? ' > ' . $department->parent->name : '' }}
                                </div>
                                <span class="text-muted font-italic font-12">
                                    {{ $department->description ?? '-' }}
                                </span><br>
                                @if($department->head)
                                    <span class="text-muted font-italic">
                                        Head: {{ $department->head->employee_id_full_name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="width: 100px">
                        @if($department->cost_center)
                            <span class="badge badge-classic">
                                {{ $department->cost_center }}
                            </span>
                        @endif
                    </td>
                    <td style="width: 100px" class="font-16 font-weight-bold">
                        <i class="fas fa-users"></i> 0
                    </td>
                    <td style="width: 100px" class="pr-0 text-right">
                        <a style="width: 34px" href="{{ route('departments.show', $department)  }}" class="btn btn-sm btn-outline-gray">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a style="width: 34px" href="{{ route('departments.edit', $department) }}" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>