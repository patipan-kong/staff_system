@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-edit"></i> Edit Group Project</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.group-projects.show', $groupProject) }}" class="btn btn-outline-info">
                <i class="fas fa-eye"></i> View
            </a>
            <a href="{{ route('admin.group-projects.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-layer-group"></i> Group Project Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.group-projects.update', $groupProject) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">Group Project Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $groupProject->name) }}" 
                                   placeholder="e.g., Mobile App Development, Website Redesign" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="planned" {{ old('status', $groupProject->status) == 'planned' ? 'selected' : '' }}>Planned</option>
                                <option value="active" {{ old('status', $groupProject->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ old('status', $groupProject->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="inactive" {{ old('status', $groupProject->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4" 
                                  placeholder="Brief description of the group project and its objectives...">{{ old('description', $groupProject->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="icon" class="form-label">Icon</label>
                            <select class="form-select @error('icon') is-invalid @enderror" id="icon" name="icon">
                                <option value="">Select Icon (Optional)</option>
                                <option value="fas fa-folder" {{ old('icon', $groupProject->icon) == 'fas fa-folder' ? 'selected' : '' }}>📁 Folder</option>
                                <option value="fas fa-project-diagram" {{ old('icon', $groupProject->icon) == 'fas fa-project-diagram' ? 'selected' : '' }}>📊 Project Diagram</option>
                                <option value="fas fa-laptop-code" {{ old('icon', $groupProject->icon) == 'fas fa-laptop-code' ? 'selected' : '' }}>💻 Development</option>
                                <option value="fas fa-mobile-alt" {{ old('icon', $groupProject->icon) == 'fas fa-mobile-alt' ? 'selected' : '' }}>📱 Mobile</option>
                                <option value="fas fa-globe" {{ old('icon', $groupProject->icon) == 'fas fa-globe' ? 'selected' : '' }}>🌐 Web</option>
                                <option value="fas fa-paint-brush" {{ old('icon', $groupProject->icon) == 'fas fa-paint-brush' ? 'selected' : '' }}>🎨 Design</option>
                                <option value="fas fa-chart-line" {{ old('icon', $groupProject->icon) == 'fas fa-chart-line' ? 'selected' : '' }}>📈 Analytics</option>
                                <option value="fas fa-cog" {{ old('icon', $groupProject->icon) == 'fas fa-cog' ? 'selected' : '' }}>⚙️ System</option>
                                <option value="fas fa-rocket" {{ old('icon', $groupProject->icon) == 'fas fa-rocket' ? 'selected' : '' }}>🚀 Launch</option>
                                <option value="fas fa-lightbulb" {{ old('icon', $groupProject->icon) == 'fas fa-lightbulb' ? 'selected' : '' }}>💡 Innovation</option>
                            </select>
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Icon will be displayed with the group project</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="color" class="form-label">Color</label>
                            <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                   id="color" name="color" value="{{ old('color', $groupProject->color ?? '#007bff') }}" 
                                   style="width: 100%; height: 38px;">
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Theme color for the group project</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6>Preview</h6>
                                <div class="d-inline-block p-3 rounded" 
                                     style="background-color: {{ $groupProject->color ?? '#007bff' }}20; border: 2px solid {{ $groupProject->color ?? '#007bff' }}" 
                                     id="preview-card">
                                    <i class="{{ $groupProject->icon ?? 'fas fa-folder' }} fa-2x mb-2" 
                                       style="color: {{ $groupProject->color ?? '#007bff' }}" 
                                       id="preview-icon"></i>
                                    <h6 id="preview-name">{{ $groupProject->name }}</h6>
                                    <span class="badge bg-info" id="preview-status">{{ ucfirst($groupProject->status) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.group-projects.show', $groupProject) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Update Group Project
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const statusSelect = document.getElementById('status');
    const iconSelect = document.getElementById('icon');
    const colorInput = document.getElementById('color');
    
    const previewCard = document.getElementById('preview-card');
    const previewIcon = document.getElementById('preview-icon');
    const previewName = document.getElementById('preview-name');
    const previewStatus = document.getElementById('preview-status');
    
    const statusClasses = {
        'active': 'bg-success',
        'inactive': 'bg-secondary',
        'planned': 'bg-info',
        'completed': 'bg-primary'
    };

    function updatePreview() {
        const name = nameInput.value || 'Group Project Name';
        const status = statusSelect.value || 'planned';
        const icon = iconSelect.value || 'fas fa-folder';
        const color = colorInput.value || '#007bff';
        
        previewName.textContent = name;
        previewStatus.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        previewStatus.className = 'badge ' + (statusClasses[status] || 'bg-info');
        previewIcon.className = icon + ' fa-2x mb-2';
        previewIcon.style.color = color;
        previewCard.style.backgroundColor = color + '20';
        previewCard.style.borderColor = color;
    }
    
    nameInput.addEventListener('input', updatePreview);
    statusSelect.addEventListener('change', updatePreview);
    iconSelect.addEventListener('change', updatePreview);
    colorInput.addEventListener('input', updatePreview);
    
    // Initial preview update
    updatePreview();
});
</script>
@endpush
