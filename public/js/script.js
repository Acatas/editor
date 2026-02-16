// Drag and drop functionality
document.querySelectorAll('.file-upload').forEach(upload => {
    upload.addEventListener('dragover', (e) => {
        e.preventDefault();
        upload.classList.add('dragover');
    });

    upload.addEventListener('dragleave', () => {
        upload.classList.remove('dragover');
    });

    upload.addEventListener('drop', (e) => {
        e.preventDefault();
        upload.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        const input = upload.querySelector('input[type="file"]');
        input.files = files;
        
        const listId = input.id === 'jpeg-files' ? 'jpeg-file-list' : 'image-file-list';
        displaySelectedFiles(input.id, listId);
    });
});

function displaySelectedFiles(inputId, listId) {
    const input = document.getElementById(inputId);
    const list = document.getElementById(listId);
    const files = input.files;

    if (files.length > 0) {
        list.style.display = 'block';
        list.innerHTML = '<strong>Selected files:</strong><br>';
        
        for (let i = 0; i < files.length; i++) {
            const fileSize = (files[i].size / 1024 / 1024).toFixed(2);
            list.innerHTML += `<div class="file-item">
                <span>${files[i].name}</span>
                <span>${fileSize} MB</span>
            </div>`;
        }
    } else {
        list.style.display = 'none';
    }
}

function processRename() {
    const files = document.getElementById('jpeg-files').files;
    const output = document.getElementById('rename-output');
    const btn = document.getElementById('rename-btn');
    const progress = document.getElementById('rename-progress');
    const progressBar = document.getElementById('rename-progress-bar');
    const btnSpinner = btn.querySelector('.spinner-border');

    if (files.length === 0) {
        output.className = 'output alert alert-danger mt-3';
        output.innerHTML = '❌ Please select JPEG files first.';
        return;
    }

    // Prepare form data
    const formData = new FormData();
    for (let i = 0; i < files.length; i++) {
        formData.append('files[]', files[i]);
    }
    formData.append('action', 'rename');

    // Show progress and disable button
    btn.disabled = true;
    btnSpinner.classList.remove('d-none');
    btn.childNodes[1].nodeValue = ' Processing...'; // Update button text
    progress.style.display = 'flex';
    progressBar.style.width = '0%';
    progressBar.setAttribute('aria-valuenow', '0');
    output.innerHTML = ''; // Clear previous output
    output.className = 'output alert mt-3'; // Reset alert class

    // Upload and process
    const xhr = new XMLHttpRequest();
    
    xhr.upload.addEventListener('progress', (e) => {
        if (e.lengthComputable) {
            const percentComplete = (e.loaded / e.total) * 50; // Upload is 50% of progress
            progressBar.style.width = percentComplete + '%';
            progressBar.setAttribute('aria-valuenow', percentComplete);
        }
    });

    xhr.addEventListener('load', () => {
        progressBar.style.width = '100%';
        progressBar.setAttribute('aria-valuenow', '100');
        
        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                
                if (response.success) {
                    output.className = 'output alert alert-success mt-3';
                    output.innerHTML = `✅ Successfully processed ${response.processed} files!`;
                    
                    if (response.download_url) {
                        output.innerHTML += `<div class="mt-3">
                            <a href="${response.download_url}" class="btn btn-success" download>
                                📥 Download Renamed Files (${response.zip_name})
                            </a>
                        </div>`;
                    }
                } else {
                    output.className = 'output alert alert-danger mt-3';
                    output.innerHTML = `❌ Error: ${response.message}`;
                }
            } catch (e) {
                output.className = 'output alert alert-danger mt-3';
                output.innerHTML = '❌ An unexpected error occurred. Could not parse server response.';
                console.error('Parsing error:', e);
            }
        } else {
            output.className = 'output alert alert-danger mt-3';
            output.innerHTML = '❌ Server error occurred.';
        }

        // Reset button and hide progress
        btn.disabled = false;
        btnSpinner.classList.add('d-none');
        btn.childNodes[1].nodeValue = ' Rename to JPG';
        setTimeout(() => {
            progress.style.display = 'none';
            progressBar.style.width = '0%';
            progressBar.setAttribute('aria-valuenow', '0');
        }, 1000);
    });

    xhr.addEventListener('error', () => {
        output.className = 'output alert alert-danger mt-3';
        output.innerHTML = '❌ Network error occurred.';
        btn.disabled = false;
        btnSpinner.classList.add('d-none');
        btn.childNodes[1].nodeValue = ' Rename to JPG';
        progress.style.display = 'none';
    });

    xhr.open('POST', 'src/php/process.php');
    xhr.send(formData);
}

function processResize() {
    const files = document.getElementById('image-files').files;
    const output = document.getElementById('resize-output');
    const btn = document.getElementById('resize-btn');
    const progress = document.getElementById('resize-progress');
    const progressBar = document.getElementById('resize-progress-bar');
    const btnSpinner = btn.querySelector('.spinner-border');


    if (files.length === 0) {
        output.className = 'output alert alert-danger mt-3';
        output.innerHTML = '❌ Please select image files first.';
        return;
    }

    // Prepare form data
    const formData = new FormData();
    for (let i = 0; i < files.length; i++) {
        formData.append('files[]', files[i]);
    }
    formData.append('action', 'resize');

    // Show progress and disable button
    btn.disabled = true;
    btnSpinner.classList.remove('d-none');
    btn.childNodes[1].nodeValue = ' Processing...'; // Update button text
    progress.style.display = 'flex';
    progressBar.style.width = '0%';
    progressBar.setAttribute('aria-valuenow', '0');
    output.innerHTML = ''; // Clear previous output
    output.className = 'output alert mt-3'; // Reset alert class

    // Upload and process
    const xhr = new XMLHttpRequest();
    
    xhr.upload.addEventListener('progress', (e) => {
        if (e.lengthComputable) {
            const percentComplete = (e.loaded / e.total) * 50; // Upload is 50% of progress
            progressBar.style.width = percentComplete + '%';
            progressBar.setAttribute('aria-valuenow', percentComplete);
        }
    });

    xhr.addEventListener('load', () => {
        progressBar.style.width = '100%';
        progressBar.setAttribute('aria-valuenow', '100');
        
        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                
                if (response.success) {
                    output.className = 'output alert alert-success mt-3';
                    output.innerHTML = `✅ Successfully processed ${response.processed} images!`;
                    
                    if (response.download_url) {
                        output.innerHTML += `<div class="mt-3">
                            <a href="${response.download_url}" class="btn btn-success" download>
                                📥 Download Processed Images (${response.zip_name})
                            </a>
                        </div>`;
                    }
                } else {
                    output.className = 'output alert alert-danger mt-3';
                    output.innerHTML = `❌ Error: ${response.message}`;
                }
            } catch (e) {
                output.className = 'output alert alert-danger mt-3';
                output.innerHTML = '❌ An unexpected error occurred. Could not parse server response.';
                console.error('Parsing error:', e);
            }
        } else {
            output.className = 'output alert alert-danger mt-3';
            output.innerHTML = '❌ Server error occurred.';
        }

        // Reset button and hide progress
        btn.disabled = false;
        btnSpinner.classList.add('d-none');
        btn.childNodes[1].nodeValue = ' Resize & Center';
        setTimeout(() => {
            progress.style.display = 'none';
            progressBar.style.width = '0%';
            progressBar.setAttribute('aria-valuenow', '0');
        }, 1000);
    });

    xhr.addEventListener('error', () => {
        output.className = 'output alert alert-danger mt-3';
        output.innerHTML = '❌ Network error occurred.';
        btn.disabled = false;
        btnSpinner.classList.add('d-none');
        btn.childNodes[1].nodeValue = ' Resize & Center';
        progress.style.display = 'none';
    });

    xhr.open('POST', 'src/php/process.php');
    xhr.send(formData);
}
