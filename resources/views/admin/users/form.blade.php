<form method="POST"
      action="{{ $action }}">

    @csrf

    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="row g-3">

        <div class="col-md-6">
            <label class="form-label">
                Full Name
                <span class="text-danger">*</span>
            </label>

            <input type="text"
                   name="name"
                   class="form-control"
                   maxlength="255"
                   value="{{ old(
                       'name',
                       $user->name ?? ''
                   ) }}"
                   required>
        </div>

        <div class="col-md-6">
            <label class="form-label">
                Email
                <span class="text-danger">*</span>
            </label>

            <input type="email"
                   name="email"
                   class="form-control"
                   maxlength="255"
                   value="{{ old(
                       'email',
                       $user->email ?? ''
                   ) }}"
                   required>
        </div>

        <div class="col-md-6">
            <label class="form-label">
                Role
            </label>

            <select name="role_id"
                    class="form-select">

                <option value="">No Role</option>

                @foreach($roles as $role)
                    <option value="{{ $role->id }}"
                        @selected(
                            (string) old(
                                'role_id',
                                $user->role_id ?? ''
                            ) ===
                            (string) $role->id
                        )>

                        {{ $role->display_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">
                Engagement Points
                <span class="text-danger">*</span>
            </label>

            <input type="number"
                   name="engagement_points"
                   min="0"
                   step="1"
                   class="form-control"
                   value="{{ old(
                       'engagement_points',
                       $user->engagement_points ?? 0
                   ) }}"
                   required>
        </div>

        <div class="col-md-6">
            <label class="form-label">
                Password
                @if(!isset($user))
                    <span class="text-danger">*</span>
                @endif
            </label>

            <input type="password"
                   name="password"
                   class="form-control"
                   minlength="8"
                   @required(!isset($user))>

            @if(isset($user))
                <small class="text-muted">
                    Leave blank to keep the current password.
                </small>
            @endif
        </div>

        @if(!isset($user) && isset($clubs))

            <div class="col-md-6">
                <label class="form-label">
                    Initial Club
                </label>

                <select name="club_id"
                        class="form-select">

                    <option value="">
                        No initial club
                    </option>

                    @foreach($clubs as $club)
                        <option value="{{ $club->id }}"
                            @selected(
                                old('club_id') == $club->id
                            )>
                            {{ $club->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">
                    Club Member Role
                </label>

                <select name="member_role"
                        class="form-select">

                    @foreach([
                        'member',
                        'executive',
                        'president',
                        'secretary',
                        'treasurer',
                        'coordinator'
                    ] as $value)

                        <option value="{{ $value }}"
                            @selected(
                                old(
                                    'member_role',
                                    'member'
                                ) === $value
                            )>
                            {{ ucfirst($value) }}
                        </option>

                    @endforeach
                </select>
            </div>

        @endif

        <div class="col-12">
            <hr>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.users.index') }}"
                   class="btn btn-outline-secondary">
                    Cancel
                </a>

                <button type="submit"
                        class="btn btn-primary">

                    <i class="bi bi-check-circle me-1"></i>
                    Save User
                </button>
            </div>
        </div>

    </div>
</form>