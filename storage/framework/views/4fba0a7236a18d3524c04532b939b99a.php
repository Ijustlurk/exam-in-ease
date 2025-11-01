

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
    <div class="modal-content" style="border-radius: 15px; border: none;">
      <!-- Header -->
      <div class="modal-header" style="background: #6ba5bb; color: white; border-radius: 15px 15px 0 0; border: none;">
        <h5 class="modal-title d-flex align-items-center" id="addUserModalLabel">
          <i class="bi bi-person-plus-fill me-2"></i> Add User
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body" style="padding: 2rem;">
        <form id="addUserForm" action="<?php echo e(route('admin.users.store')); ?>" method="POST">
          <?php echo csrf_field(); ?>
          
          <!-- Role Selection -->
          <div class="mb-4">
            <label class="form-label" style="font-weight: 600; font-size: 0.9rem; color: #333;">Role</label>
            <div class="d-flex gap-2 flex-wrap">
              <input type="radio" class="btn-check" name="role" id="studentRole" value="student" checked required>
              <label class="btn btn-outline-secondary" for="studentRole" style="border-radius: 20px; padding: 0.4rem 1.2rem; font-size: 0.9rem;">Student</label>
              
              <input type="radio" class="btn-check" name="role" id="instructorRole" value="instructor">
              <label class="btn btn-outline-secondary" for="instructorRole" style="border-radius: 20px; padding: 0.4rem 1.2rem; font-size: 0.9rem;">Instructor</label>
              
              <input type="radio" class="btn-check" name="role" id="programChairRole" value="programchair">
              <label class="btn btn-outline-secondary" for="programChairRole" style="border-radius: 20px; padding: 0.4rem 1.2rem; font-size: 0.9rem;">Program Chair</label>
              
              <input type="radio" class="btn-check" name="role" id="adminRole" value="admin">
              <label class="btn btn-outline-secondary" for="adminRole" style="border-radius: 20px; padding: 0.4rem 1.2rem; font-size: 0.9rem;">Admin</label>
            </div>
          </div>

          <!-- Identification (Student Only) -->
          <div id="identificationSection" class="mb-4">
            <label class="form-label" style="font-weight: 600; font-size: 0.9rem; color: #333;">Identification</label>
            <input type="text" class="form-control" placeholder="Student ID Number" name="id_number" id="idNumberInput" maxlength="50" style="border-radius: 8px; padding: 0.6rem 1rem;">
          </div>

          <!-- User Details (Not for Admin) -->
          <div id="userDetailsSection" class="mb-4">
            <label class="form-label" style="font-weight: 600; font-size: 0.9rem; color: #333;">User Details</label>
            <div class="mb-2">
              <input type="text" class="form-control" placeholder="Last Name" name="last_name" id="lastNameInput" maxlength="100" style="border-radius: 8px; padding: 0.6rem 1rem;">
            </div>
            <div class="mb-2">
              <input type="text" class="form-control" placeholder="First Name" name="first_name" id="firstNameInput" maxlength="100" style="border-radius: 8px; padding: 0.6rem 1rem;">
            </div>
            <div class="mb-2">
              <input type="text" class="form-control" placeholder="Middle Name (Optional)" name="middle_name" id="middleNameInput" maxlength="100" style="border-radius: 8px; padding: 0.6rem 1rem;">
            </div>
          </div>

          <!-- Authentication -->
          <div class="mb-4">
            <label class="form-label" style="font-weight: 600; font-size: 0.9rem; color: #333;">Authentication</label>
            
            <!-- Email (Not for Admin) -->
            <div class="mb-2" id="emailSection">
              <input type="email" class="form-control" placeholder="Email Address" name="email_address" id="emailInput" maxlength="150" style="border-radius: 8px; padding: 0.6rem 1rem;">
            </div>
            
            <!-- Username (Not for Student) -->
            <div class="mb-2" id="usernameSection">
              <input type="text" class="form-control" placeholder="Username" name="username" id="usernameInput" maxlength="100" style="border-radius: 8px; padding: 0.6rem 1rem;">
            </div>
            
            <!-- Password -->
            <div class="mb-2">
              <input type="password" class="form-control" placeholder="Password" name="password" id="passwordInput" required minlength="4" style="border-radius: 8px; padding: 0.6rem 1rem;">
            </div>
          </div>

          <!-- Submit Button -->
          <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn d-flex align-items-center" style="background: #6ba5bb; color: white; border: none; border-radius: 8px; padding: 0.6rem 2rem; font-weight: 600;">
              <i class="bi bi-plus-circle me-2"></i>Add User
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
#addUserModal .form-control {
  border: 1px solid #e0e0e0;
  font-size: 0.9rem;
}

#addUserModal .form-control:focus {
  border-color: #6ba5bb;
  box-shadow: 0 0 0 0.2rem rgba(107, 165, 187, 0.15);
}

#addUserModal .btn-check:checked + .btn-outline-secondary {
  background-color: #6ba5bb;
  border-color: #6ba5bb;
  color: white;
}

#addUserModal .btn-outline-secondary {
  border-color: #dee2e6;
  color: #6c757d;
}

#addUserModal .btn-outline-secondary:hover {
  background-color: #f8f9fa;
  border-color: #dee2e6;
  color: #6c757d;
}

#addUserModal .modal-body {
  max-height: 70vh;
  overflow-y: auto;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const roleInputs = document.querySelectorAll('input[name="role"]');
  const identificationSection = document.getElementById('identificationSection');
  const userDetailsSection = document.getElementById('userDetailsSection');
  const emailSection = document.getElementById('emailSection');
  const usernameSection = document.getElementById('usernameSection');
  
  const idNumberInput = document.getElementById('idNumberInput');
  const usernameInput = document.getElementById('usernameInput');
  const emailInput = document.getElementById('emailInput');
  const lastNameInput = document.getElementById('lastNameInput');
  const firstNameInput = document.getElementById('firstNameInput');
  const middleNameInput = document.getElementById('middleNameInput');
  
  // Function to update form based on role
  function updateFormByRole(role) {
    // Reset all required attributes
    idNumberInput.required = false;
    usernameInput.required = false;
    emailInput.required = false;
    lastNameInput.required = false;
    firstNameInput.required = false;
    
    // Hide all sections initially
    identificationSection.style.display = 'none';
    userDetailsSection.style.display = 'none';
    emailSection.style.display = 'none';
    usernameSection.style.display = 'none';
    
    // Clear values
    idNumberInput.value = '';
    usernameInput.value = '';
    emailInput.value = '';
    lastNameInput.value = '';
    firstNameInput.value = '';
    middleNameInput.value = '';
    
    switch(role) {
      case 'student':
        // Student: ID Number + User Details + Email
        identificationSection.style.display = 'block';
        userDetailsSection.style.display = 'block';
        emailSection.style.display = 'block';
        
        idNumberInput.required = true;
        emailInput.required = true;
        lastNameInput.required = true;
        firstNameInput.required = true;
        break;
        
      case 'instructor':
      case 'programchair':
        // Instructor/Chair: User Details + Email + Username
        userDetailsSection.style.display = 'block';
        emailSection.style.display = 'block';
        usernameSection.style.display = 'block';
        
        emailInput.required = true;
        usernameInput.required = true;
        lastNameInput.required = true;
        firstNameInput.required = true;
        break;
        
      case 'admin':
        // Admin: Only Username
        usernameSection.style.display = 'block';
        usernameInput.required = true;
        break;
    }
  }
  
  // Initialize form on load
  const checkedRole = document.querySelector('input[name="role"]:checked');
  if (checkedRole) {
    updateFormByRole(checkedRole.value);
  }
  
  // Listen to role changes
  roleInputs.forEach(input => {
    input.addEventListener('change', function() {
      updateFormByRole(this.value);
    });
  });
  
  // Reset form when modal is closed
  const modal = document.getElementById('addUserModal');
  if (modal) {
    modal.addEventListener('hidden.bs.modal', function() {
      document.getElementById('addUserForm').reset();
      updateFormByRole('student'); // Reset to default
    });
  }
});
</script><?php /**PATH C:\xampp\htdocs\exam1\resources\views/admin/users/partials/add-user-modal.blade.php ENDPATH**/ ?>