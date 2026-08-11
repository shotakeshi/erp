<div class="table-responsive mt-3">
    <table class="table mb-0 table-centered table-hover">
        <thead class="bg-light">
            <tr>
                <td>
                    {{ __('site.positions.name') }}
                </td>
                <td>
                    {{ __('site.positions.department') }}
                </td>
                <td>
                    {{ __('site.positions.level_grade') }}
                </td>
                <td>
                    {{ __('site.positions.salary_range') }}
                </td>
                <td style="width: 100px" class="text-center">
                    <i class="fa fa-users"></i>
                </td>
                <td style="width: 150px" class="text-center">
                </td>
            </tr>
        </thead>
        <tbody>
            @foreach($positions as $position)
                <tr>
                    <td>
                        {{ $position->name }}<br>
                        <span class="text-muted font-italic font-12">
                            {{ $position->description ?? '-' }}
                        </span>
                    </td>
                    <td>
                        {{ $position->department?->name }}
                    </td>
                    <td>
                        {{ $position->level }}
                    </td>
                    <td>
                        {{ $position->salary_min }} - {{ $position->salary_max }}
                    </td>
                    <td class="text-center">
                        {{ $position->employees_count }}
                    </td>
                    <td class="text-center">
                        <a style="width: 34px" href="{{ route('positions.edit', $position) }}" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <x-form.delete-button
                          :action="route('positions.destroy',$position)"
                        />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>