
<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
    <div class="modal-content" style="border-radius: 15px; border: none;">
      <!-- Header -->
      <div class="modal-header" style="background-color: #6ba5bb; color: white; border-radius: 15px 15px 0 0; border: none;">
        <h5 class="modal-title d-flex align-items-center" id="editUserModalLabel">
          <i class="bi bi-pencil-fill me-2"></i> Edit User
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body" style="padding: 2rem;">
        <form id="editUserForm" action="" method="POST">
          <?php echo csrf_field(); ?>
          <?php echo method_field('PUT'); ?>
          
          <!-- Hidden Role Field -->
          <input type="hidden" id="editUserRole" name="role">
          
          <!-- Role Display (Read-only) -->
          <div class="mb-4">
            <label class="form-label" style="font-weight: 600; font-size: 0.9rem; color: #333;">Role</label>
            <input type="text" id="editRoleDisplay" class="form-control" readonly style="border-radius: 8px; padding: 0.6rem 1rem; background-color: #f8f9fa;">
          </div>
          
          <!-- Identification Section (Student Only) -->
          <div id="editIdentificationSection" class="mb-4" style="display: none;">
            <label class="form-label" style="font-weight: 600; font-size: 0.9rem; color: #333;">Identification</label>
            <input type="text" class="form-control" placeholder="Student ID Number" name="id_number" id="editIdNumberInput" maxlength="50" style="border-radius: 8px; padding: 0.6rem 1rem;">
          </div>

          <!-- User Details Section (Not for Admin) -->
          <div id="editUserDetailsSection" class="mb-4" style="display: none;">
            <label class="form-label" style="font-weight: 600; font-size: 0.9rem; color: #333;">User Details</label>
            <div class="mb-2">
              <input type="text" class="form-control" placeholder="Last Name" name="last_name" id="editLastNameInput" maxlength="100" style="border-radius: 8px; padding: 0.6rem 1rem;">
            </div>
            <div class="mb-2">
              <input type="text" class="form-control" placeholder="First Name" name="first_name" id="editFirstNameInput" maxlength="100" style="border-radius: 8px; padding: 0.6rem 1rem;">
            </div>
            <div class="mb-2">
              <input type="text" class="form-control" placeholder="Middle Name (Optional)" name="middle_name" id="editMiddleNameInput" maxlength="100" style="border-radius: 8px; padding: 0.6rem 1rem;">
            </div>
          </div>

          <!-- Authentication Section -->
          <div class="mb-4">
            <label class="form-label" style="font-weight: 600; font-size: 0.9rem; color: #333;">Authentication</label>
            
            <!-- Email (Not for Admin) -->
            <div class="mb-2" id="editEmailSection" style="display: none;">
              <input type="email" class="form-control" placeholder="Email Address" name="email_address" id="editEmailInput" maxlength="150" style="border-radius: 8px; padding: 0.6rem 1rem;">
            </div>
            
            <!-- Username (Not for Student) -->
            <div class="mb-2" id="editUsernameSection" style="display: none;">
              <input type="text" class="form-control" placeholder="Username" name="username" id="editUsernameInput" maxlength="100" style="border-radius: 8px; padding: 0.6rem 1rem;">
            </div>
            
            <!-- Password (Optional for Edit) -->
            <div class="mb-2">
              <input type="password" class="form-control" placeholder="New Password (leave blank to keep current)" name="password" id="editPasswordInput" minlength="4" style="border-radius: 8px; padding: 0.6rem 1rem;">
              <small class="text-muted">Leave blank if you don't want to change the password</small>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; padding: 0.6rem 1.5rem;">Cancel</button>
            <button type="submit" class="btn" style="background-color: #6ba5bb; color: white; border: none; border-radius: 8px; padding: 0.6rem 2rem; font-weight: 600;">
              <i class="bi bi-check-circle me-2"></i>Update User
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
#editUserModal .form-control {
  border: 1px solid #e0e0e0;
  font-size: 0.9rem;
}

#editUserModal .form-control:focus {
  border-color: #6ba5bb;
  box-shadow: 0 0 0 0.2rem rgba(107, 165, 187, 0.15);
}

#editUserModal .form-control:read-only {
  background-color: #f8f9fa;
  cursor: not-allowed;
}

#editUserModal .modal-body {
  max-height: 70vh;
  overflow-y: auto;
}
</style>

<script>
// Function to update edit form based on role
function updateEditFormByRole(role) {
  const editIdentificationSection = document.getElementById('editIdentificationSection');
  const editUserDetailsSection = document.getElementById('editUserDetailsSection');
  const editEmailSection = document.getElementById('editEmailSection');
  const editUsernameSection = document.getElementById('editUsernameSection');
  
  const editIdNumberInput = document.getElementById('editIdNumberInput');
  const editUsernameInput = document.getElementById('editUsernameInput');
  const editEmailInput = document.getElementById('editEmailInput');
  const editLastNameInput = document.getElementById('editLastNameInput');
  const editFirstNameInput = document.getElementById('editFirstNameInput');
  const editPasswordInput = document.getElementById('editPasswordInput');
  
  // Reset all required attributes
  editIdNumberInput.required = false;
  editUsernameInput.required = false;
  editEmailInput.required = false;
  editLastNameInput.required = false;
  editFirstNameInput.required = false;
  editPasswordInput.required = false;
  
  // Hide all sections initially
  editIdentificationSection.style.display = 'none';
  editUserDetailsSection.style.display = 'none';
  editEmailSection.style.display = 'none';
  editUsernameSection.style.display = 'none';
  
  switch(role) {
    case 'student':
      editIdentificationSection.style.display = 'block';
      editUserDetailsSection.style.display = 'block';
      editEmailSection.style.display = 'block';
      
      editIdNumberInput.required = true;
      editEmailInput.required = true;
      editLastNameInput.required = true;
      editFirstNameInput.required = true;
      break;
      
    case 'instructor':
    case 'programchair':
      editUserDetailsSection.style.display = 'block';
      editEmailSection.style.display = 'block';
      editUsernameSection.style.display = 'block';
      
      editEmailInput.required = true;
      editUsernameInput.required = true;
      editLastNameInput.required = true;
      editFirstNameInput.required = true;
      break;
      
    case 'admin':
      editUsernameSection.style.display = 'block';
      editUsernameInput.required = true;
      break;
  }
}

// Function to open edit modal with user data
function openEditUserModal(userId) {
  // Fetch user data via AJAX
  fetch(`/admin/users/${userId}/edit`, {
    headers: {
      'Accept': 'application/json',
    }
  })
  .then(response => response.json())
  .then(data => {
    if (!data.success) {
      alert('Error loading user data: ' + (data.message || 'Unknown error'));
      return;
    }
    
    const user = data.user;
    const editForm = document.getElementById('editUserForm');
    const editUserRole = document.getElementById('editUserRole');
    const editRoleDisplay = document.getElementById('editRoleDisplay');
    
    // Set form action
    editForm.action = `/admin/users/${userId}`;
    
    // Set role
    editUserRole.value = user.role;
    const roleDisplayName = user.role === 'programchair' ? 'Program Chair' : user.role.charAt(0).toUpperCase() + user.role.slice(1);
    editRoleDisplay.value = roleDisplayName;
    
    // Update form based on role
    updateEditFormByRole(user.role);
    
    // Populate fields based on role
    if (user.role === 'student') {
      document.getElementById('editIdNumberInput').value = user.id_number || '';
      document.getElementById('editLastNameInput').value = user.last_name || '';
      document.getElementById('editFirstNameInput').value = user.first_name || '';
      document.getElementById('editMiddleNameInput').value = user.middle_name || '';
      document.getElementById('editEmailInput').value = user.email_address || '';
    } else if (user.role === 'instructor' || user.role === 'chair') {
      document.getElementById('editLastNameInput').value = user.last_name || '';
      document.getElementById('editFirstNameInput').value = user.first_name || '';
      document.getElementById('editMiddleNameInput').value = user.middle_name || '';
      document.getElementById('editEmailInput').value = user.email_address || '';
      document.getElementById('editUsernameInput').value = user.username || '';
    } else if (user.role === 'admin') {
      document.getElementById('editUsernameInput').value = user.username || '';
    }
    
    // Clear password field
    document.getElementById('editPasswordInput').value = '';
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
    modal.show();
  })
  .catch(error => {
    console.error('Error fetching user data:', error);
    alert('Error loading user data. Please try again.');
  });
}

// Reset form when modal is closed
document.addEventListener('DOMContentLoaded', function() {
  const editModal = document.getElementById('editUserModal');
  if (editModal) {
    editModal.addEventListener('hidden.bs.modal', function() {
      document.getElementById('editUserForm').reset();
    });
  }
});
</script><?php /**PATH C:\xampp\htdocs\exam1\resources\views/admin/users/partials/edit-user-modal.blade.php ENDPATH**/ ?>