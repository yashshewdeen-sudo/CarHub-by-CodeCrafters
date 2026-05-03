@extends('layouts.app')
@section('title', 'Sell Your Car')
@section('content')
    <h2>List Your Car</h2>
    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    <form method="POST" action="{{ route('listings.store') }}" enctype="multipart/form-data" class="card p-4">
        @csrf
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Make</label>
                <input name="make" class="form-control" value="{{ old('make') }}" required></div>
            <div class="col-md-6"><label class="form-label">Model</label>
                <input name="model" class="form-control" value="{{ old('model') }}" required></div>
            <div class="col-md-3"><label class="form-label">Year</label>
                <input type="number" name="year" min="1900" max="{{ date('Y')+1 }}" class="form-control" value="{{ old('year') }}" required></div>
            <div class="col-md-3"><label class="form-label">Mileage</label>
                <input type="number" name="mileage" min="0" class="form-control" value="{{ old('mileage') }}" required></div>
            <div class="col-md-3"><label class="form-label">Price (Rs)</label>
                <input type="number" name="price" min="1" step="0.01" class="form-control" value="{{ old('price') }}" required></div>
            <div class="col-md-3"><label class="form-label">Condition</label>
                <select name="condition_status" class="form-select"><option>Used</option><option>New</option></select></div>
            <div class="col-md-4"><label class="form-label">Fuel</label>
                <select name="fuel_type" class="form-select">
                    @foreach (['Petrol','Diesel','Hybrid','Electric'] as $f)<option>{{ $f }}</option>@endforeach
                </select></div>
            <div class="col-md-4"><label class="form-label">Transmission</label>
                <select name="transmission" class="form-select">
                    @foreach (['Manual','Automatic','CVT'] as $t)<option>{{ $t }}</option>@endforeach
                </select></div>
            <div class="col-md-4"><label class="form-label">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">— None —</option>
                    @foreach ($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                </select></div>
            <div class="col-12">
                <label class="form-label">Tags</label>
                <select name="tags[]" class="form-select" multiple>
                    @foreach ($tags as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
                <small class="text-muted">Hold Ctrl / Cmd to select multiple. Demonstrates many-to-many relation.</small>
            </div>
            <div class="col-12">
                <label class="form-label">Photos (max 5)</label>
                <input type="file" name="images[]" id="imageInput" multiple accept="image/*" class="form-control">
            </div>

            <div class="col-12" id="previewContainer" style="display:none;">
                <label class="form-label">Select Cover Photo</label>
                <p class="text-muted small">Click on a photo to set it as the cover</p>
                <div class="d-flex gap-2 flex-wrap" id="previewList"></div>
                <input type="hidden" name="cover_index" id="coverIndex" value="0">
            </div>
            <div class="col-12"><label class="form-label">Description</label>
                <textarea name="description" rows="4" class="form-control">{{ old('description') }}</textarea></div>
        </div>
        <button class="btn btn-primary mt-3">Submit</button>
    </form>

<script>
document.getElementById('imageInput').addEventListener('change', function() {
    const container = document.getElementById('previewContainer');
    const previewList = document.getElementById('previewList');
    const coverIndex = document.getElementById('coverIndex');
    
    previewList.innerHTML = '';
    
    if (this.files.length === 0) {
        container.style.display = 'none';
        return;
    }
    
    container.style.display = 'block';
    
    Array.from(this.files).forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const wrapper = document.createElement('div');
            wrapper.style.cursor = 'pointer';
            wrapper.style.position = 'relative';
            
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.height = '100px';
            img.style.width = '150px';
            img.style.objectFit = 'cover';
            img.className = 'rounded border border-2';
            
            const badge = document.createElement('span');
            badge.className = 'badge bg-success position-absolute top-0 start-0 m-1';
            badge.innerText = 'Cover';
            badge.style.display = i === 0 ? 'block' : 'none';
            
            if (i === 0) {
                img.classList.add('border-success');
            }
            
            wrapper.appendChild(img);
            wrapper.appendChild(badge);
            
            wrapper.addEventListener('click', function() {
                // Remove cover from all
                document.querySelectorAll('#previewList img').forEach(i => {
                    i.classList.remove('border-success');
                });
                document.querySelectorAll('#previewList .badge').forEach(b => {
                    b.style.display = 'none';
                });
                
                // Set this as cover
                img.classList.add('border-success');
                badge.style.display = 'block';
                coverIndex.value = i;
            });
            
            previewList.appendChild(wrapper);
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endsection