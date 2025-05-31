document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const navbar = document.querySelector('.navbar');
    
    mobileMenuBtn.addEventListener('click', function() {
        navbar.classList.toggle('active');
    });

    // Material tabs functionality
    const tabBtns = document.querySelectorAll('.tab-btn');
    const materialsGrid = document.getElementById('materialsGrid');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            tabBtns.forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            // In a real implementation, you would fetch filtered materials here
            // For now, we'll just simulate loading
            simulateMaterialLoading(this.dataset.tab);
        });
    });

    // Simulate loading materials (replace with actual API call in production)
    function simulateMaterialLoading(type) {
        materialsGrid.innerHTML = `
            <div class="skeleton-card"></div>
            <div class="skeleton-card"></div>
            <div class="skeleton-card"></div>
            <div class="skeleton-card"></div>
        `;
        
        // Simulate API delay
        setTimeout(() => {
            // This would be replaced with actual materials from your database
            materialsGrid.innerHTML = generateSampleMaterials(type);
        }, 800);
    }

    // Generate sample materials (replace with real data from your backend)
    function generateSampleMaterials(type) {
        const materials = [
            {
                id: 1,
                title: "Thermodynamics Lecture Series",
                dept: "Mechanical",
                type: "videos",
                course: "ME205",
                uploadDate: "3 days ago",
                rating: 4.8
            },
            {
                id: 2,
                title: "Data Structures Complete Notes",
                dept: "Computer Science",
                type: "notes",
                course: "CS201",
                uploadDate: "1 week ago",
                rating: 4.5
            },
            {
                id: 3,
                title: "Circuit Theory PDF Guide",
                dept: "Electrical",
                type: "pdfs",
                course: "EE202",
                uploadDate: "2 days ago",
                rating: 4.2
            },
            {
                id: 4,
                title: "Fluid Mechanics Lab Manual",
                dept: "Mechanical",
                type: "docs",
                course: "ME210",
                uploadDate: "5 days ago",
                rating: 4.7
            }
        ];

        // Filter by type if not "all"
        let filteredMaterials = type === 'all' 
            ? materials 
            : materials.filter(m => m.type === type);

        // Generate HTML for materials
        return filteredMaterials.map(material => `
            <div class="material-card">
                <div class="material-type ${material.type}">
                    <i class="${getMaterialIcon(material.type)}"></i>
                </div>
                <div class="material-info">
                    <h3>${material.title}</h3>
                    <div class="material-meta">
                        <span class="dept-badge">${material.dept}</span>
                        <span>${material.course}</span>
                    </div>
                    <div class="material-footer">
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <span>${material.rating}</span>
                        </div>
                        <span class="upload-date">${material.uploadDate}</span>
                    </div>
                </div>
            </div>
        `).join('');
    }

    function getMaterialIcon(type) {
        const icons = {
            'videos': 'fas fa-video',
            'notes': 'fas fa-file-alt',
            'pdfs': 'fas fa-file-pdf',
            'docs': 'fas fa-file-word'
        };
        return icons[type] || 'fas fa-file';
    }

    // Initialize with "all" materials
    simulateMaterialLoading('all');
});