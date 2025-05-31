<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Engineering E-Learning Platform</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navigation Bar -->
    <header class="header">
        <div class="container">
            <div class="logo">
                <i class="fas fa-graduation-cap"></i>
                <h1>Engineering Animutyam</h1>
            </div>
            <nav class="navbar">
                <ul>
                    <li><a href="index.php" class="active">Home</a></li>
                    <li><a href="#dept">Departments</a></li>
                    <li><a href="#mtrls">Study Materials</a></li>
                    <li><a href="#features">About</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <a href="index.php" class="btn login-btn">Login</a>
                <a href="student/register.php" class="btn register-btn">Register</a>
            </div>
            <div class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h2>Empowering Engineering Students</h2>
                <p>Access department-wise study materials, video lectures, notes, and more for all regulations</p>
                <div class="hero-buttons">
                    <a href="index.php" class="btn primary-btn">Browse Departments</a>
                    <a href="#features" class="btn secondary-btn">Learn More</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="learn.jpg" alt="Engineering Students Learning">
            </div>
        </div>
    </section>

    <!-- Departments Quick Access -->
    <section class="departments-section" id="dept">
        <div class="container">
            <h2 class="section-title">Explore Departments</h2>
            <div class="departments-grid">
                <a href="index.php" class="department-card">
                    <i class="fas fa-cogs"></i>
                    <h3>Mechanical Engineering</h3>
                    <p>Study materials for all semesters</p>
                </a>
                <a href="index.php" class="department-card">
                    <i class="fas fa-microchip"></i>
                    <h3>Electronics & Communication</h3>
                    <p>Videos, notes and question banks</p>
                </a>
                <a href="index.php" class="department-card">
                    <i class="fas fa-laptop-code"></i>
                    <h3>Computer Science</h3>
                    <p>Programming resources and labs</p>
                </a>
                <a href="index.php" class="department-card">
                    <i class="fas fa-bolt"></i>
                    <h3>Electrical Engineering</h3>
                    <p>Circuit diagrams and theory</p>
                </a>
                <a href="index.php" class="department-card">
                    <i class="fas fa-building"></i>
                    <h3>Civil Engineering</h3>
                    <p>Design and construction materials</p>
                </a>
                <a href="index.php" class="department-card">
                    <i class="fas fa-atom"></i>
                    <h3>Chemical Engineering</h3>
                    <p>Process and reactor design</p>
                </a>
            </div>
            <div class="view-all">
                <a href="#" class="btn outline-btn">View All Departments</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="container">
            <h2 class="section-title">Why Choose Our Platform</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h3>Comprehensive Materials</h3>
                    <p>Access notes, PDFs, presentations, and video lectures for all engineering subjects.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-filter"></i>
                    </div>
                    <h3>Department-wise Organization</h3>
                    <p>Materials organized by department, regulation, and semester for easy navigation.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3>Student Reviews</h3>
                    <p>See ratings and reviews from other students to find the most helpful resources.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h3>Faculty Uploads</h3>
                    <p>Verified materials uploaded by department faculty members.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Advanced Search</h3>
                    <p>Quickly find materials by subject code, professor name, or keywords.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Mobile Friendly</h3>
                    <p>Access all materials on any device, anytime, anywhere.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Materials Section -->
    <section class="materials-section" id="mtrls">
        <div class="container">
            <h2 class="section-title">Recently Added Materials</h2>
            <div class="materials-tabs">
                <button class="tab-btn active" data-tab="all">All</button>
                <button class="tab-btn" data-tab="videos">Videos</button>
                <button class="tab-btn" data-tab="notes">Notes</button>
                <button class="tab-btn" data-tab="pdfs">PDFs</button>
                <button class="tab-btn" data-tab="docs">Docs</button>
            </div>
            <div class="materials-grid" id="materialsGrid">
                <!-- Materials will be loaded here by JavaScript -->
                <div class="skeleton-card"></div>
                <div class="skeleton-card"></div>
                <div class="skeleton-card"></div>
                <div class="skeleton-card"></div>
            </div>
            <div class="view-all">
                <a href="#" class="btn outline-btn">View All Materials</a>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section" id="stdnts">
        <div class="container">
            <h2 class="section-title">What Students Say</h2>
            <div class="testimonials-slider">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"This platform helped me tremendously during my exams. The department-wise organization made it easy to find exactly what I needed."</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="uploads/kavya.jpg" alt="Student">
                        <div>
                            <h4>Y Kavya</h4>
                            <p>CSE, 4th Year</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"The video lectures from professors are gold! I can revisit difficult concepts anytime. Highly recommended for all engineering students."</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="uploads/bhavani.jpg" alt="Student">
                        <div>
                            <h4>V BhavaniShankar</h4>
                            <p>CSE, 4th Year</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"As a visual learner, the combination of notes and video explanations works perfectly for me. This platform is a game-changer!"</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="uploads/prem.jpg" alt="Student">
                        <div>
                            <h4>K PremSagar</h4>
                            <p>CSE, 4th Year</p>
                        </div>
                    </div>
                </div>
                   <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"As a visual learner, the combination of notes and video explanations works perfectly for me. This platform is a game-changer!"</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="uploads/sai.jpg" alt="Student">
                        <div>
                            <h4>P Sai Kiran</h4>
                            <p>CSE, 4th Year</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Enhance Your Learning?</h2>
                <p>Join thousands of engineering students who are already benefiting from our platform</p>
                <div class="cta-buttons">
                    <a href="student/register.php" class="btn primary-btn">Get Started Now</a>
                    <a href="#" class="btn secondary-btn">Learn How It Works</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="logo">
                        <i class="fas fa-graduation-cap"></i>
                        <h3>EngLearn</h3>
                    </div>
                    <p>Empowering engineering students with quality learning resources since 2023.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="#">Home</a></li>
                        <li><a href="#dept">Departments</a></li>
                        <li><a href="#mtrls">Study Materials</a></li>
                        <li><a href="#features">About Us</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Departments</h4>
                    <ul>
                        <li><a href="#">Mechanical</a></li>
                        <li><a href="#">Computer Science</a></li>
                        <li><a href="#">Electronics</a></li>
                        <li><a href="#">Electrical</a></li>
                        <li><a href="#">Civil</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Help Center</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2023 EngLearn. All rights reserved.</p>
                <div class="footer-links">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                    <a href="https://srisivani.com/" target="_blank">Sitemap</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="js/home.js"></script>
</body>
</html>