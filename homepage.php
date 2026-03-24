<?php
session_start();
include("connect.php");

// १. युजर लॉगिन नसेल तर index.php कडे पाठवा
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email'];
$query = mysqli_query($conn, "SELECT firstName FROM users WHERE email='$email'");
$user = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Happy Paws | Premium Pet Care & Guidance</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        :root {
            --primary: #FF7033;
            --primary-dark: #e6561e;
            --secondary: #1A1C1E;
            --accent-cream: #FDF4ED;
            --white: #ffffff;
            --card-shadow: 0 15px 35px rgba(255, 112, 51, 0.1);
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; color: var(--secondary); background: #fff; scroll-behavior: smooth; }

        /* --- NAVBAR --- */
        .navbar { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-bottom: 1px solid #eee; padding: 15px 0; }
        .navbar-brand { font-weight: 800; color: var(--primary) !important; font-size: 24px; }
        .nav-link { font-weight: 700; color: var(--secondary); font-size: 14px; }

        /* --- AI TALK BUTTON --- */
        .ai-talk-btn {
            background: linear-gradient(135deg, var(--primary), #FFB185);
            color: white !important;
            font-weight: 800;
            font-size: 14px;
            padding: 10px 25px;
            border-radius: 50px;
            border: none;
            box-shadow: 0 10px 20px rgba(255, 112, 51, 0.3);
            transition: all 0.4s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: glow-pulse 2s infinite;
        }
        .ai-talk-btn:hover { transform: translateY(-3px); box-shadow: 0 15px 25px rgba(255, 112, 51, 0.4); background: var(--secondary); }
        @keyframes glow-pulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 112, 51, 0.4); }
            70% { box-shadow: 0 0 0 12px rgba(255, 112, 51, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 112, 51, 0); }
        }

        /* --- HERO --- */
        .hero-section { background: radial-gradient(circle at top right, var(--accent-cream), #ffffff); padding: 100px 0 60px; border-radius: 0 0 50px 50px; }
        .hero-title { font-weight: 800; font-size: 3.5rem; }
        .hero-title span { color: var(--primary); }
        .hero-image img { 
            width: 100%; border-radius: 50px; 
            box-shadow: 30px 30px 80px rgba(255, 112, 51, 0.2); 
            animation: float 4s ease-in-out infinite; 
        }
        @keyframes float { 0%, 100% { transform: translateY(0) rotate(2deg); } 50% { transform: translateY(-20px) rotate(-1deg); } }

        /* --- SERVICES --- */
        .section-padding { padding: 80px 0; }
        .service-card { background: white; padding: 30px; border-radius: 25px; transition: 0.3s; border: 1px solid #f0f0f0; height: 100%; text-align: center; }
        .service-card:hover { transform: translateY(-10px); box-shadow: var(--card-shadow); border-color: var(--primary); }
        .icon-box { font-size: 40px; margin-bottom: 15px; }

        /* --- QUIZ --- */
        .interactive-box { background: var(--secondary); color: white; border-radius: 40px; padding: 50px; }
        .quiz-option-btn { border-radius: 15px; padding: 12px 25px; border: 1px solid rgba(255,255,255,0.2); color: white; margin: 8px; transition: 0.3s; background: rgba(255,255,255,0.05); }
        .quiz-option-btn:hover { background: var(--primary); border-color: var(--primary); transform: scale(1.05); }

        /* --- CHATBOT WIDGET --- */
        #chat-widget { position: fixed; bottom: 25px; right: 25px; z-index: 1000; }
        .chat-bubble { width: 65px; height: 65px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 30px; box-shadow: 0 10px 25px rgba(255,112,51,0.4); transition: 0.3s; }
        .chat-window { position: absolute; bottom: 85px; right: 0; width: 320px; height: 480px; background: white; border-radius: 25px; display: none; flex-direction: column; box-shadow: 0 15px 50px rgba(0,0,0,0.15); overflow: hidden; border: 1px solid #eee; }
        .chat-header { background: var(--primary); color: white; padding: 20px; font-weight: 800; text-align: center; }
        .chat-body { flex: 1; padding: 20px; overflow-y: auto; background: #fafafa; display: flex; flex-direction: column; gap: 12px; }
        .chat-footer { padding: 15px; border-top: 1px solid #eee; display: flex; gap: 8px; }
        .chat-footer input { flex: 1; border: 1px solid #ddd; border-radius: 20px; padding: 8px 15px; outline: none; }
        .msg { padding: 10px 15px; border-radius: 18px; font-size: 14px; max-width: 85%; }
        .bot-msg { background: var(--accent-cream); align-self: flex-start; border-bottom-left-radius: 2px; color: #444; }
        .user-msg { background: var(--primary); color: white; align-self: flex-end; border-bottom-right-radius: 2px; }

        /* --- FOOTER --- */
        .footer { background: var(--secondary); color: #ccc; padding: 80px 0 30px; border-radius: 60px 60px 0 0; }
        .footer h5 { color: var(--primary); font-weight: 800; }
        .footer-links a { color: #aaa; text-decoration: none; transition: 0.3s; display: block; margin-bottom: 10px; }
        .footer-links a:hover { color: var(--primary); transform: translateX(5px); }
        .hidden { display: none !important; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand animate__animated animate__fadeInLeft" href="#">🐾 Happy Paws</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="ms-auto d-flex align-items-center gap-3">
                    <ul class="navbar-nav me-3">
                        <li class="nav-item"><a class="nav-link" href="homepage.html">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="aboutsection.html">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="adoption.html">Adoption</a></li>
                        <li class="nav-item"><a class="nav-link" href="service.html">Services</a></li>
                        <li class="nav-item"><a class="nav-link" href="trial2.html">Blog</a></li>
                    </ul>
                    <a href="trial.html" class="ai-talk-btn">
                        <span style="background:rgba(255,255,255,0.2); border-radius:50%; width:24px; height:24px; display:flex; align-items:center; justify-content:center; font-size:12px;"></span>
                        AI Talk
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 animate__animated animate__fadeInUp">
                    <h1 class="hero-title">Your Pet's <span>Happiness</span> Is Our Mission</h1>
                    <p class="lead text-muted my-4">Expert care, professional boarding, and grooming services tailored for your furry family members.</p>
                    <button class="btn btn-lg px-5 text-white shadow" style="background: var(--primary); border-radius: 50px; font-weight: 700;" onclick="showHeroFact()">Explore More ✨</button>
                    
                    <div id="hero-fact-box" class="mt-4 p-3 bg-white border-start border-4 border-warning shadow-sm hidden" style="border-radius: 15px;">
                        <p id="hero-fact-text" class="mb-0 fw-bold text-primary"></p>
                    </div>
                </div>
                <div class="col-lg-6 text-center animate__animated animate__zoomIn">
                    <img src="https://images.unsplash.com/photo-1450778869180-41d0601e046e?auto=format&fit=crop&q=80&w=1000" class="hero-image img-fluid" alt="Golden Retriever">
                </div>
            </div>
        </div>
    </section>

    <section id="services" class="section-padding">
        <div class="container text-center">
            <span class="badge rounded-pill px-3 py-2 mb-3" style="background: var(--accent-cream); color: var(--primary); font-weight: 700;">WHAT WE DO</span>
            <h2 class="display-5 fw-bold mb-5">Our Premium Services</h2>
            <div class="row g-4 text-start">
                <div class="col-md-3">
                    <div class="service-card">
                        <div class="icon-box">🛁</div>
                        <h5 class="fw-bold">Bath & Spa</h5>
                        <p class="small text-muted">A relaxing, therapeutic experience for your pet.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="service-card">
                        <div class="icon-box">🏃‍♂️</div>
                        <h5 class="fw-bold">Daily Walk</h5>
                        <p class="small text-muted">Keeping them active, healthy, and happy.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="service-card">
                        <div class="icon-box">🛌</div>
                        <h5 class="fw-bold">Pet Boarding</h5>
                        <p class="small text-muted">A safe home away from home for your pet.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="service-card">
                        <div class="icon-box">✂️</div>
                        <h5 class="fw-bold">Hair Styling</h5>
                        <p class="small text-muted">Professional grooming for a stylish look.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="quiz" class="container mb-5">
        <div class="interactive-box text-center shadow-lg">
            <h2 class="fw-bold mb-4">Pet Personality Quiz 🐾</h2>
            <div id="quiz-content">
                <h4 id="q-text" class="mb-4">1. How does your pet react to a stranger?</h4>
                <div class="d-flex flex-wrap justify-content-center">
                    <button class="btn quiz-option-btn" onclick="handleQuiz('A')">Protective Barking 🐕</button>
                    <button class="btn quiz-option-btn" onclick="handleQuiz('B')">Instant Cuddles ❤️</button>
                    <button class="btn quiz-option-btn" onclick="handleQuiz('C')">Quiet Observation 🤫</button>
                </div>
            </div>
            <div id="quiz-result" class="hidden">
                <div class="bg-white text-dark p-5 rounded-4 shadow-sm">
                    <h3 id="res-title" class="text-primary fw-800"></h3>
                    <p id="res-desc" class="lead mb-0"></p>
                </div>
                <button class="btn btn-outline-light mt-4 px-4 rounded-pill" onclick="resetQuiz()">Try Again 🔄</button>
            </div>
        </div>
    </section>

    <div id="chat-widget">
        <div class="chat-window" id="chat-window">
            <div class="chat-header">Happy Paws Assistant 🐾</div>
            <div class="chat-body" id="chat-body">
                <div class="msg bot-msg">Hi! How can I help you? Choose a quick question:</div>
                <div class="d-flex flex-wrap gap-2 mt-2" id="quick-replies">
                    <button class="btn btn-outline-primary btn-sm rounded-pill" style="font-size:11px;" onclick="askBot('Timings?')">🕒 Timings</button>
                    <button class="btn btn-outline-primary btn-sm rounded-pill" style="font-size:11px;" onclick="askBot('Location?')">📍 Location</button>
                    <button class="btn btn-outline-primary btn-sm rounded-pill" style="font-size:11px;" onclick="askBot('Prices?')">💰 Prices</button>
                </div>
            </div>
            <div class="chat-footer">
                <input type="text" id="chat-input" placeholder="Ask me something...">
                <button class="btn btn-primary rounded-circle" onclick="sendMessage()"><i class="fa fa-paper-plane"></i></button>
            </div>
        </div>
        <div class="chat-bubble" onclick="toggleChat()">🐶</div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="row g-5">
                <div class="col-md-4">
                    <h5>🐾 Happy Paws</h5>
                    <p class="mt-3">Providing world-class pet care and guidance since 2026. Your pet's safety and joy are our top priorities.</p>
                    <div class="d-flex gap-3 mt-4">
                        <a href="#" class="text-white fs-4"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-white fs-4"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white fs-4"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                <div class="col-md-2 offset-md-1">
                    <h5>Quick Links</h5>
                    <div class="footer-links mt-3">
                        <a href="#">About Us</a>
                        <a href="#services">Services</a>
                        <a href="#quiz">Quiz</a>
                        <a href="pet-ai.html">AI Consultant</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <h5>Contact</h5>
                    <p class="small mt-3">📍 123 Pet Street, Mumbai<br>📞 +91 98765 43210<br>✉️ help@happypaws.com</p>
                </div>
            </div>
            <hr class="mt-5 opacity-25">
            <p class="text-center small mb-0 opacity-75">© 2026 Happy Paws Care. Crafted with Love 🧡</p>
        </div>
    </footer>

    <script>
        // Hero Facts Logic
        function showHeroFact() {
            const facts = [
                "A dog's nose print is as unique as a fingerprint! 👃",
                "Cats spend 70% of their lives sleeping. 🐱",
                "Dogs can sense time and miss you when you're gone! ⌛"
            ];
            const factBox = document.getElementById('hero-fact-box');
            factBox.classList.remove('hidden');
            document.getElementById('hero-fact-text').innerText = facts[Math.floor(Math.random() * facts.length)];
            confetti({ particleCount: 100, spread: 70, origin: { y: 0.8 } });
        }

        // Quiz Logic
        let step = 1;
        function handleQuiz(val) {
            if(step === 1) {
                document.getElementById('q-text').innerText = "2. What is their favorite toy?";
                step++;
            } else {
                document.getElementById('quiz-content').classList.add('hidden');
                document.getElementById('quiz-result').classList.remove('hidden');
                document.getElementById('res-title').innerText = "The Guardian 🛡️";
                document.getElementById('res-desc').innerText = "Your pet is a natural protector, incredibly loyal, and always has your back!";
                confetti({ particleCount: 200, spread: 100 });
            }
        }
        function resetQuiz() {
            step = 1;
            document.getElementById('quiz-content').classList.remove('hidden');
            document.getElementById('quiz-result').classList.add('hidden');
            document.getElementById('q-text').innerText = "1. How does your pet react to a stranger?";
        }

        // Chatbot Logic
        function toggleChat() {
            const win = document.getElementById('chat-window');
            win.style.display = (win.style.display === 'flex' ? 'none' : 'flex');
        }

        const responses = {
            "Timings?": "We are open 9 AM - 8 PM (Mon-Sat). Sunday: 10 AM - 4 PM.",
            "Location?": "Visit us at 123 Pet Street, near Central Park, Mumbai.",
            "Prices?": "Spa starts at ₹499, Boarding at ₹899/day. Click 'AI Talk' for more advice!"
        };

        function askBot(ques) {
            addMsg(ques, 'user-msg');
            setTimeout(() => {
                addMsg(responses[ques] || "That's a great question! For specific advice, try our AI Talk section.", 'bot-msg');
            }, 600);
        }

        function sendMessage() {
            const input = document.getElementById('chat-input');
            if(!input.value.trim()) return;
            addMsg(input.value, 'user-msg');
            setTimeout(() => addMsg("Thanks! Our team will reply shortly. Or check out our AI Talk page! 🐾", 'bot-msg'), 800);
            input.value = '';
        }

        function addMsg(text, type) {
            const div = document.createElement('div');
            div.className = 'msg ' + type + ' animate__animated animate__fadeInUp';
            div.innerText = text;
            const body = document.getElementById('chat-body');
            body.appendChild(div);
            body.scrollTop = body.scrollHeight;
        }
    </script>
</body>
</html>