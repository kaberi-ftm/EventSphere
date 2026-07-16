@extends('layouts.admin')

@section('title', 'Edit Certificate')
@section('page-title', 'Edit Certificate')

@section('content')
<div class="container-fluid px-0">
    <div class="card shadow-sm">

        <div class="card-header">
            <h4 class="mb-0">
                {{ $certificate->certificate_number }}
            </h4>
        </div>

        <div class="card-body">
            <form method="POST"
                  action="{{ route(
                      'admin.certificates.update',
                      $certificate->id
                  ) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Title</label>

                    <input name="title"
                           class="form-control"
                           value="{{ old(
                               'title',
                               $certificate->title
                           ) }}"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Description
                    </label>

                    <textarea name="description"
                              rows="4"
                              class="form-control">{{ old(
                                  'description',
                                  $certificate->description
                              ) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>

                    <select name="status"
                            class="form-select"
                            required>
                        <option value="issued"
                            @selected(
                                old(
                                    'status',
                                    $certificate->status
                                ) === 'issued'
                            )>
                            Issued
                        </option>

                        <option value="revoked"
                            @selected(
                                old(
                                    'status',
                                    $certificate->status
                                ) === 'revoked'
                            )>
                            Revoked
                        </option>
                    </select>
                </div>

                <div class="text-end">
                    <a href="{{ route(
                        'admin.certificates.show',
                        $certificate->id
                    ) }}"
                       class="btn btn-outline-secondary">
                        Cancel
                    </a>

                    <button class="btn btn-primary">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection