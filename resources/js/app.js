import './bootstrap';

// === Dashboard Apprenant Moderne JS ===

// Animation au scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
        }
    });
}, observerOptions);

document.querySelectorAll('.animate-on-scroll').forEach(el => {
    observer.observe(el);
});

// Création du graphique de progression
window.addEventListener('DOMContentLoaded', function() {
    const chartEl = document.getElementById('progressChart');
    if (chartEl && window.Chart) {
        const ctx = chartEl.getContext('2d');
        const percent = 85;
        const color = percent >= 60 ? '#10b981' : '#dc3545';
        const bgColor = 'rgba(255, 255, 255, 0.2)';
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [percent, 100 - percent],
                    backgroundColor: [color, bgColor],
                    borderWidth: 0,
                    borderRadius: 10
                }]
            },
            options: {
                cutout: '75%',
                plugins: {
                    tooltip: { enabled: false },
                    legend: { display: false }
                },
                animation: {
                    animateRotate: true,
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            },
            plugins: [{
                id: 'centerText',
                afterDraw: function(chart) {
                    const { width, height, ctx } = chart;
                    ctx.restore();
                    const fontSize = (height / 4).toFixed(2);
                    ctx.font = `bold ${fontSize}px Arial`;
                    ctx.textBaseline = 'middle';
                    ctx.fillStyle = 'white';
                    ctx.textAlign = 'center';
                    const text = percent + '%';
                    ctx.fillText(text, width / 2, height / 2);
                    ctx.save();
                }
            }]
        });
    }
});

// Création des particules flottantes
function createParticles() {
    const particles = document.getElementById('particles');
    if (!particles) return;
    const particleCount = 50;
    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.top = Math.random() * 100 + '%';
        particle.style.animationDelay = Math.random() * 6 + 's';
        particle.style.animationDuration = (Math.random() * 3 + 3) + 's';
        particles.appendChild(particle);
    }
}

// Animation des barres de progression
function animateProgressBars() {
    const progressBars = document.querySelectorAll('.progress-bar-animated');
    progressBars.forEach((bar, index) => {
        setTimeout(() => {
            bar.style.width = bar.style.width;
        }, index * 200);
    });
}

// Effet de parallaxe sur les cartes
window.addEventListener('mousemove', (e) => {
    const cards = document.querySelectorAll('.card-3d');
    const mouseX = e.clientX / window.innerWidth;
    const mouseY = e.clientY / window.innerHeight;
    cards.forEach(card => {
        const rect = card.getBoundingClientRect();
        const cardX = rect.left + rect.width / 2;
        const cardY = rect.top + rect.height / 2;
        const centerX = window.innerWidth / 2;
        const centerY = window.innerHeight / 2;
        const rotateX = (mouseY - 0.5) * 5;
        const rotateY = (mouseX - 0.5) * 5;
        if (card.matches(':hover')) {
            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-10px)`;
        }
    });
});

// Effet de typing sur les titres
function typeWriter(element, text, speed = 100) {
    let i = 0;
    element.innerHTML = '';
    function type() {
        if (i < text.length) {
            element.innerHTML += text.charAt(i);
            i++;
            setTimeout(type, speed);
        }
    }
    type();
}

// Animation des badges au survol
document.querySelectorAll('.badge-3d').forEach(badge => {
    badge.addEventListener('mouseenter', function() {
        this.style.transform = 'scale(1.1) rotate(5deg)';
    });
    badge.addEventListener('mouseleave', function() {
        this.style.transform = 'scale(1) rotate(0deg)';
    });
});

// Effet de shake sur les boutons
document.querySelectorAll('.btn-3d').forEach(btn => {
    btn.addEventListener('click', function() {
        this.style.animation = 'pulse 0.6s ease-in-out';
        setTimeout(() => {
            this.style.animation = '';
        }, 600);
    });
});

// Initialisation des animations
document.addEventListener('DOMContentLoaded', function() {
    createParticles();
    animateProgressBars();
    // Animation d'entrée pour le titre principal
    const mainTitle = document.querySelector('.card-header-gradient h2');
    if (mainTitle) {
        const originalText = mainTitle.textContent;
        setTimeout(() => {
            typeWriter(mainTitle, originalText, 50);
        }, 500);
    }
    if(document.getElementById('dashboardParticles')) {
        // Animation de particules déjà gérée dans le blade, mais on peut relancer ici si besoin
        if(typeof createDashboardParticles === 'function') createDashboardParticles();
    }
});

// Effet de couleur dynamique sur les éléments
function createColorWave() {
    const elements = document.querySelectorAll('.card-header-gradient');
    elements.forEach((element, index) => {
        setTimeout(() => {
            element.style.background = `linear-gradient(135deg, 
                hsl(${120 + Math.sin(Date.now() * 0.001 + index) * 30}, 70%, 50%) 0%, 
                hsl(${140 + Math.cos(Date.now() * 0.001 + index) * 30}, 70%, 40%) 100%)`;
        }, index * 100);
    });
}
setInterval(createColorWave, 3000);

// Effet de ripple sur les cartes
document.querySelectorAll('.card-3d').forEach(card => {
    card.addEventListener('click', function(e) {
        const ripple = document.createElement('div');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.3) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            animation: ripple 0.6s ease-out;
        `;
        this.style.position = 'relative';
        this.appendChild(ripple);
        setTimeout(() => {
            ripple.remove();
        }, 600);
    });
});

// Ajout de l'animation CSS pour le ripple
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        0% {
            transform: scale(0);
            opacity: 1;
        }
        100% {
            transform: scale(1);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
