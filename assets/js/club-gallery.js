// Gallery data for lightbox
let galleryData = [];
let currentImageIndex = 0;

// Load gallery data from DOM
document.addEventListener('DOMContentLoaded', function() {
    const items = document.querySelectorAll('.gallery-item');
    items.forEach((item, index) => {
        const img = item.querySelector('img');
        const overlay = item.querySelector('.item-overlay');
        
        galleryData.push({
            id: index,
            src: img.src,
            title: overlay?.querySelector('h3')?.textContent || 'Ảnh CLB',
            description: overlay?.querySelector('p')?.textContent || '',
            uploader: overlay?.querySelector('.item-meta span:first-child')?.textContent || '',
            date: overlay?.querySelector('.item-meta span:last-child')?.textContent || ''
        });
    });
});

// Upload Modal
function openUploadModal() {
    document.getElementById('uploadModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeUploadModal() {
    document.getElementById('uploadModal').classList.remove('active');
    document.body.style.overflow = 'auto';
    document.getElementById('uploadForm').reset();
    document.getElementById('previewContainer').innerHTML = '';
}

// File upload handling
const uploadArea = document.getElementById('uploadArea');
const imageInput = document.getElementById('imageInput');
const previewContainer = document.getElementById('previewContainer');

if (uploadArea && imageInput) {
    uploadArea.addEventListener('click', () => imageInput.click());
    
    // Drag and drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        imageInput.files = files;
        handleFiles(files);
    });
    
    imageInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });
}

function handleFiles(files) {
    previewContainer.innerHTML = '';
    
    Array.from(files).forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            
            reader.onload = (e) => {
                const previewItem = document.createElement('div');
                previewItem.className = 'preview-item';
                previewItem.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <button type="button" class="preview-remove" onclick="removePreview(${index})">×</button>
                `;
                previewContainer.appendChild(previewItem);
            };
            
            reader.readAsDataURL(file);
        }
    });
    
    // Hide placeholder if files selected
    if (files.length > 0) {
        document.querySelector('.upload-placeholder').style.display = 'none';
    }
}

function removePreview(index) {
    // This is simplified - in production you'd need to handle file removal properly
    const previews = previewContainer.querySelectorAll('.preview-item');
    if (previews[index]) {
        previews[index].remove();
    }
    
    if (previewContainer.children.length === 0) {
        document.querySelector('.upload-placeholder').style.display = 'block';
    }
}

// Lightbox
function openLightbox(imageId) {
    currentImageIndex = imageId;
    updateLightbox();
    document.getElementById('lightbox').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    document.getElementById('lightbox').classList.remove('active');
    document.body.style.overflow = 'auto';
}

function updateLightbox() {
    if (galleryData.length === 0) return;
    
    const data = galleryData[currentImageIndex];
    document.getElementById('lightboxImage').src = data.src;
    document.getElementById('lightboxTitle').textContent = data.title;
    document.getElementById('lightboxDescription').textContent = data.description;
    document.getElementById('lightboxUploader').textContent = data.uploader;
    document.getElementById('lightboxDate').textContent = data.date;
}

function prevImage() {
    currentImageIndex = (currentImageIndex - 1 + galleryData.length) % galleryData.length;
    updateLightbox();
}

function nextImage() {
    currentImageIndex = (currentImageIndex + 1) % galleryData.length;
    updateLightbox();
}

// Keyboard navigation
document.addEventListener('keydown', (e) => {
    const lightbox = document.getElementById('lightbox');
    if (lightbox && lightbox.classList.contains('active')) {
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') prevImage();
        if (e.key === 'ArrowRight') nextImage();
    }
    
    const modal = document.getElementById('uploadModal');
    if (modal && modal.classList.contains('active')) {
        if (e.key === 'Escape') closeUploadModal();
    }
});

// Close modal on outside click
document.getElementById('uploadModal')?.addEventListener('click', (e) => {
    if (e.target.id === 'uploadModal') {
        closeUploadModal();
    }
});

document.getElementById('lightbox')?.addEventListener('click', (e) => {
    if (e.target.id === 'lightbox') {
        closeLightbox();
    }
});
