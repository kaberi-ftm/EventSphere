@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ $action }}">
    @csrf

    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Sponsor Name</label>
            <input type="text"
                   name="name"
                   class="form-control"
                   value="{{ old('name', $sponsor->name ?? '') }}"
                   required>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">Contact Person</label>
            <input type="text"
                   name="contact_person"
                   class="form-control"
                   value="{{ old('contact_person', $sponsor->contact_person ?? '') }}">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <input type="email"
                   name="email"
                   class="form-control"
                   value="{{ old('email', $sponsor->email ?? '') }}">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">Phone</label>
            <input type="text"
                   name="phone"
                   class="form-control"
                   value="{{ old('phone', $sponsor->phone ?? '') }}">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">Website</label>
            <input type="url"
                   name="website"
                   class="form-control"
                   value="{{ old('website', $sponsor->website ?? '') }}">
        </div>

        <div class="col-md-3 mb-3">
            <label class="form-label">Type</label>
            <select name="sponsor_type" class="form-select" required>
                @foreach(['corporate','individual','ngo','media','government','other'] as $type)
                    <option value="{{ $type }}"
                        @selected(old('sponsor_type', $sponsor->sponsor_type ?? 'corporate') === $type)>
                        {{ ucfirst($type) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
                <option value="active"
                    @selected(old('status', $sponsor->status ?? 'active') === 'active')>
                    Active
                </option>
                <option value="inactive"
                    @selected(old('status', $sponsor->status ?? '') === 'inactive')>
                    Inactive
                </option>
            </select>
        </div>

        <div class="col-12 mb-3">
            <label class="form-label">Address</label>
            <textarea name="address"
                      class="form-control">{{ old('address', $sponsor->address ?? '') }}</textarea>
        </div>

        <div class="col-12 mb-3">
            <label class="form-label">Description</label>
            <textarea name="description"
                      rows="4"
                      class="form-control">{{ old('description', $sponsor->description ?? '') }}</textarea>
        </div>
    </div>

    <button class="btn btn-primary">Save Sponsor</button>

    <a href="{{ route('admin.sponsors.index') }}"
       class="btn btn-secondary">
        Cancel
    </a>
</form>