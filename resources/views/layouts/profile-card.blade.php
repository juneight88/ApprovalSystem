<!-- Profile Card Component -->
<div class="card">
    <div class="card-body text-center">
        <!-- Profile Picture -->
        <div class="mb-3">
            @php
                $user = Session::get('user');
            @endphp
            @if($user && $user->profile_picture)
                <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                     class="profile-pic" 
                     style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd;" 
                     alt="Profile Picture">
            @else
                <img src="https://via.placeholder.com/120" 
                     class="profile-pic"
                     style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd;"
                     alt="Default Profile Picture">
            @endif
        </div>
        
        <!-- User Info -->
        @if($user)
            <h5 class="mb-1">{{ $user->first_name }} {{ $user->last_name }}</h5>
            <p class="text-muted mb-2">{{ $user->role }}</p>
            <p class="text-muted mb-3 small">{{ $user->department }}</p>
            
            <!-- Edit Profile Button -->
            <button type="button" class="btn btn-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                <i class="fas fa-edit me-1"></i> Edit Profile
            </button>
        @endif
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/profile/update" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <!-- First Name -->
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" name="first_name" 
                               value="{{ $user->first_name }}" required>
                    </div>
                    
                    <!-- Last Name -->
                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="last_name" 
                               value="{{ $user->last_name }}" required>
                    </div>
                    
                    <!-- Profile Picture -->
                    <div class="mb-3">
                        <label class="form-label">Profile Picture</label>
                        @if($user->profile_picture)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                                     class="profile-pic"
                                     style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd;"
                                     alt="Current Profile Picture">
                                <small class="d-block text-muted">Current Profile Picture</small>
                            </div>
                        @endif
                        <input type="file" class="form-control" name="profile_picture" 
                               accept="image/jpeg,image/png,image/jpg">
                        <div class="form-text">Supported formats: JPEG, PNG, JPG (max 2MB)</div>
                    </div>
                    
                    <!-- Signature -->
                    <div class="mb-3">
                        <label class="form-label">Signature</label>
                        @if($user->signature)
                            <div class="mb-2">
                                <img src="{{ url('storage/' . $user->signature) }}" 
                                     style="max-width: 150px; height: auto;"
                                     alt="Current Signature">
                                <small class="d-block text-muted">Current Signature</small>
                            </div>
                        @endif
                        <input type="file" class="form-control" name="signature" 
                               accept="image/png">
                        <div class="form-text">PNG format only (max 2MB)</div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
