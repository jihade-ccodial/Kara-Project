<div class="member-list-container">
    <div class="member-list d-flex align-center">
        @isset($members)
            @foreach($members as $member)
                <a href="{{ url('/') }}/client/profile/{{ $member->id }}" class="member-avatar-link" data-toggle="tooltip" data-bs-custom-class="warning-tooltip" data-bs-placement="top" title="{{ $member->getFullNameAttribute() }}">
                    <div class="v-avatar member-avatar" style="height: 36px; min-width: 36px; width: 36px; margin: 0; font-size: 14px">
                        <span> {{ $member->firstName[0] . $member->lastName[0] }} </span>
                    </div>
                </a>
            @endforeach
        @endisset
    </div>
</div>
