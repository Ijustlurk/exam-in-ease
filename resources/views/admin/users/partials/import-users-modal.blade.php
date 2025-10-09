{{-- partials/import-users-modal.blade.php --}}

<!-- Import Users Modal -->
<div class="modal fade" id="importUsersModal" tabindex="-1" aria-labelledby="importUsersModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 700px;">
    <div class="modal-content" style="border-radius: 15px; border: none;">
      <!-- Header -->
      <div class="modal-header" style="background: #6ba5bb; color: white; border-radius: 15px 15px 0 0; border: none;">
        <h5 class="modal-title d-flex align-items-center" id="importUsersModalLabel">
          <i class="bi bi-file-earmark-arrow-up-fill me-2"></i> Import Users
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body" style="padding: 2rem;">
        <!-- Instructions -->
        <div class="alert alert-info" style="border-radius: 10px;">
          <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-2"></i>Import Instructions:</h6>
          <ul class="mb-0" style="font-size: 0.9rem;">
            <li>Upload a CSV or Excel file (.csv, .xlsx, .xls)</li>
            <li>File must include headers in the first row</li>
            <li>Required columns vary by role (see template below)</li>
            <li>Maximum file size: 5MB</li>
          </ul>
        </div>

        <!-- Template Download Section -->
        <div class="mb-4">
          <label class="form-label fw-bold" style="font-size: 0.95rem; color: #333;">
            <i class="bi bi-download me-2"></i>Download Template
          </label>
          <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.users.download-template', ['role' => 'student']) }}" class="btn btn-sm btn-outline-secondary" download>
              <i class="bi bi-file-earmark-spreadsheet me-1"></i> Student Template
            </a>
            <a href="{{ route('admin.users.download-template', ['role' => 'instructor']) }}" class="btn btn-sm btn-outline-secondary" download>
              <i class="bi bi-file-earmark-spreadsheet me-1"></i> Instructor Template
            </a>
            <a href="{{ route('admin.users.download-template', ['role' => 'programchair']) }}" class="btn btn-sm btn-outline-secondary" download>
              <i class="bi bi-file-earmark-spreadsheet me-1"></i> Program Chair Template
            </a>
          </div>
        </div>

        <!-- File Upload Form -->
        <form id="importUsersForm" action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data">
          @csrf
          
          <!-- Role Selection -->
          <div class="mb-4">
            <label class="form-label fw-bold" style="font-size: 0.95rem; color: #333;">
              <i class="bi bi-person-badge me-2"></i>User Role
            </label>
            <select class="form-select" name="role" id="importRoleSelect" required style="border-radius: 8px; padding: 0.7rem;">
              <option value="">Select role to import</option>
              <option value="student">Student</option>
              <option value="instructor">Instructor</option>
              <option value="programchair">Program Chair</option>
            </select>
          </div>

          <!-- File Upload Area -->
          <div class="mb-4">
            <label class="form-label fw-bold" style="font-size: 0.95rem; color: #333;">
              <i class="bi bi-cloud-upload me-2"></i>Upload File
            </label>
            <div class="file-upload-area" id="fileUploadArea" style="border: 2px dashed #6ba5bb; border-radius: 10px; padding: 2rem; text-align: center; background: #f8f9fa; cursor: pointer; transition: all 0.3s;">
              <input type="file" name="file" id="fileInput" accept=".csv,.xlsx,.xls" required style="display: none;">
              <i class="bi bi-cloud-arrow-up" style="font-size: 3rem; color: #6ba5bb;"></i>
              <p class="mb-2 mt-3 fw-bold" style="color: #333;">Click to upload or drag and drop</p>
              <p class="mb-0 text-muted" style="font-size: 0.85rem;">CSV, XLSX, or XLS (Max 5MB)</p>
              <div id="fileInfo" class="mt-3" style="display: none;">
                <i class="bi bi-file-earmark-check text-success" style="font-size: 2rem;"></i>
                <p class="mb-0 mt-2 fw-bold text-success" id="fileName"></p>
                <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="clearFile()">
                  <i class="bi bi-x-circle me-1"></i>Remove
                </button>
              </div>
            </div>
          </div>

          <!-- Progress Bar (Hidden by default) -->
          <div id="importProgress" style="display: none;">
            <div class="progress" style="height: 25px; border-radius: 10px;">
              <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%; background-color: #6ba5bb;" id="progressBar">
                <span id="progressText">0%</span>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; padding: 0.6rem 1.5rem;">Cancel</button>
            <button type="submit" class="btn" id="importBtn" style="background: #6ba5bb; color: white; border: none; border-radius: 8px; padding: 0.6rem 2rem; font-weight: 600;">
              <i class="bi bi-upload me-2"></i>Import Users
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
#importUsersModal .file-upload-area:hover {
  border-color: #5a94aa;
  background: #e8f4f8;
}

#importUsersModal .form-select:focus,
#importUsersModal .form-control:focus {
  border-color: #6ba5bb;
  box-shadow: 0 0 0 0.2rem rgba(107, 165, 187, 0.15);
}

#importUsersModal .btn-outline-secondary:hover {
  background-color: #6ba5bb;
  border-color: #6ba5bb;
  color: white;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const fileUploadArea = document.getElementById('fileUploadArea');
  const fileInput = document.getElementById('fileInput');
  const fileInfo = document.getElementById('fileInfo');
  const fileName = document.getElementById('fileName');
  const importForm = document.getElementById('importUsersForm');
  const importBtn = document.getElementById('importBtn');
  const importProgress = document.getElementById('importProgress');
  const progressBar = document.getElementById('progressBar');
  const progressText = document.getElementById('progressText');

  // Click to upload
  if (fileUploadArea) {
    fileUploadArea.addEventListener('click', function() {
      fileInput.click();
    });
  }

  // File selected
  if (fileInput) {
    fileInput.addEventListener('change', function(e) {
      if (this.files && this.files[0]) {
        const file = this.files[0];
        
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
          alert('File size must be less than 5MB');
          this.value = '';
          return;
        }

        // Validate file type
        const validTypes = ['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
        const validExtensions = ['.csv', '.xlsx', '.xls'];
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
        
        if (!validExtensions.includes(fileExtension)) {
          alert('Please upload a CSV or Excel file');
          this.value = '';
          return;
        }

        // Show file info
        fileName.textContent = file.name;
        fileInfo.style.display = 'block';
        fileUploadArea.querySelector('i.bi-cloud-arrow-up').style.display = 'none';
        fileUploadArea.querySelector('p:nth-child(2)').style.display = 'none';
        fileUploadArea.querySelector('p:nth-child(3)').style.display = 'none';
      }
    });
  }

  // Drag and drop
  if (fileUploadArea) {
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
      fileUploadArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
      e.preventDefault();
      e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
      fileUploadArea.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
      fileUploadArea.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
      fileUploadArea.style.borderColor = '#5a94aa';
      fileUploadArea.style.background = '#e8f4f8';
    }

    function unhighlight(e) {
      fileUploadArea.style.borderColor = '#6ba5bb';
      fileUploadArea.style.background = '#f8f9fa';
    }

    fileUploadArea.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
      const dt = e.dataTransfer;
      const files = dt.files;
      fileInput.files = files;
      fileInput.dispatchEvent(new Event('change'));
    }
  }

  // Form submission
  if (importForm) {
    importForm.addEventListener('submit', function(e) {
      e.preventDefault();

      const formData = new FormData(this);
      
      // Disable button and show progress
      importBtn.disabled = true;
      importBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Importing...';
      importProgress.style.display = 'block';

      // Simulate progress (will be replaced with actual progress if using AJAX)
      let progress = 0;
      const progressInterval = setInterval(() => {
        progress += 10;
        if (progress <= 90) {
          progressBar.style.width = progress + '%';
          progressText.textContent = progress + '%';
        }
      }, 200);

      // Submit form via AJAX
      fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
      .then(data => {
        clearInterval(progressInterval);
        progressBar.style.width = '100%';
        progressText.textContent = '100%';

        setTimeout(() => {
          if (data.success) {
            alert(data.message || 'Users imported successfully!');
            location.reload();
          } else {
            alert('Error: ' + (data.message || 'Failed to import users'));
            importBtn.disabled = false;
            importBtn.innerHTML = '<i class="bi bi-upload me-2"></i>Import Users';
            importProgress.style.display = 'none';
            progressBar.style.width = '0%';
          }
        }, 500);
      })
      .catch(error => {
        clearInterval(progressInterval);
        console.error('Error:', error);
        alert('An error occurred while importing users');
        importBtn.disabled = false;
        importBtn.innerHTML = '<i class="bi bi-upload me-2"></i>Import Users';
        importProgress.style.display = 'none';
        progressBar.style.width = '0%';
      });
    });
  }
});

// Clear file function
function clearFile() {
  const fileInput = document.getElementById('fileInput');
  const fileInfo = document.getElementById('fileInfo');
  const fileUploadArea = document.getElementById('fileUploadArea');
  
  fileInput.value = '';
  fileInfo.style.display = 'none';
  fileUploadArea.querySelector('i.bi-cloud-arrow-up').style.display = 'block';
  fileUploadArea.querySelector('p:nth-child(2)').style.display = 'block';
  fileUploadArea.querySelector('p:nth-child(3)').style.display = 'block';
}
</script>