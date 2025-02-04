const randomColor = () => {
    const bright = 'DB';
    const brightIndex = Math.floor(Math.random() * 2);
    let color = '#';
    for (let i = 0; i < 3; i++) {
        if (i == brightIndex) {
            color += bright;
        } else {
            color += Math.floor(Math.random() * 256).toString(16).toUpperCase().padStart(2, '0');
        }
    }
    return color;
}

class ConfettiParticle {
    width;
    height;

    constructor() {
        this.reset(true);
        this.maxY = window.innerHeight;
        this.animationDelay = Math.random() * 1000;
        
        this.random = Math.random() * 2 + 0.5;
        this.phase = Math.random() * Math.PI * 2;
        
    }

    // Visszaállítja a konfetti adatait, és randomizálja (olyan, mintha egy teljesen másik lenne)
    reset() {
        this.life = 1; // A konfetti 'élete' (1, ha most jött létre és 0, ha már a maxY-hoz közel van)
        this.color = randomColor();

        // Sebességek
        this.verticalVelocity = Math.random() * 0.5 + 0.5;
        this.horizontalVelocity = Math.random() * 0.4 - 0.2;
        this.rotationSpeed = (Math.random() - 0.5) * 0.2;
        
        // Geometriai adatok
        this.x = Math.random() * window.innerWidth;
        this.y = -Math.random() * (2000 - Math.random() * 100);

        this.initialWidth = Math.random() + 5;
        this.height = Math.random() + 10;

        this.rotation = Math.random() * 360;

        // Konstans szórzó értékek
        this.waveFactor = Math.random() * 0.5 + 0.5;
        this.speedFactor = Math.random() * 0.3 + 1.5;
    }

    // Frissíti a konfetti adatait, előréptet az időben
    update(t, dt) {
        // Frissítjük az életét, ha az élet 0, akkor pedig visszaállítjuk
        this.life = (this.maxY - this.y) / this.maxY;
        if (this.life <= 0) this.reset();

        // Az idő milliszekundumban van, ezért ezt csökkentjük
        t /= 100;

        // X-koordináta frissítése
        this.x += (Math.sin(t * this.waveFactor + this.phase) * 0.5 + Math.cos(t * 0.7 * this.waveFactor) * 0.3) * this.horizontalVelocity * dt * this.speedFactor;

        // Y-koordináta frissítése
        this.y += this.verticalVelocity * dt * (0.8 + Math.random() * 0.4);

        // Szélesség frissítése (olyan hatást ér el, mintha 3D-ben pörögne)
        this.width = this.initialWidth * Math.sin(t / 2 + this.animationDelay) * 3;

        // Elfordulási szög frissítése
        this.rotation += this.rotationSpeed * dt * (0.5 + Math.random());
    }

    handleCanvasResize() {
        this.x = Math.random() * window.innerWidth;
    }
}

class Confetti {
    running = false;
    ease = "power2.inOut";

    particleCount = 50;
    particles = [];

    elapsedTime;

    constructor(parent) {
        this.dom = document.createElement('canvas');
        this.ctx = this.dom.getContext('2d');

        this.dom.className = 'confetti-canvas';

        this.parentDom = document.querySelector(parent);
        if (!this.parentDom) throw new Error("Nem található a szülő elem.");

        this.createParticles();
        this.updateCanvasDim();
        this.parentDom.appendChild(this.dom);

        this.bindEvents();
    }

    bindEvents() {
        window.addEventListener('resize', this.updateCanvasDim.bind(this));
    }

    updateCanvasDim() {
        this.dom.width = window.innerWidth;
        this.dom.height = window.innerHeight;
    }

    createParticles() {
        for (let i = 0; i < this.particleCount; i++ ) {
            this.particles.push(new ConfettiParticle());
        }
    }

    start() {
        if (this.running) return;
        this.running = true;

        this.particles.forEach(p => p.handleCanvasResize());
        requestAnimationFrame(this.drawHandler.bind(this));

        console.log("starting confetti");
    }

    stop() {
        this.running = false;
        clearInterval(this.animationInterval);

        console.log("stopping confetti");
    }

    drawHandler() {
        this.deltaTime = this.elapsedTime ? this.elapsedTime : performance.now();
        this.elapsedTime = performance.now();
        this.deltaTime = this.elapsedTime - this.deltaTime;

        this.ctx.clearRect(0, 0, this.dom.width, this.dom.height);
        
        this.particles.forEach(e => this.drawConfetti(e));

        if (this.running) {
            requestAnimationFrame(this.drawHandler.bind(this));
        }
    }

    drawConfetti(confetti) {
        //Konfetti frissítése
        confetti.update(this.elapsedTime, this.deltaTime);

        this.ctx.save();

        // HEX szín átváltása RGBA-ra
        const hex = confetti.color.replace('#', '');

        const r = parseInt(hex.substr(0, 2), 16);
        const g = parseInt(hex.substr(2, 2), 16);
        const b = parseInt(hex.substr(4, 2), 16);

        // Áttetszőség az élet alapján
        const alpha = Math.min(confetti.life * 1.5, 1);

        this.ctx.translate(confetti.x, confetti.y);
        this.ctx.rotate(confetti.rotation * Math.PI / 180);
        this.ctx.fillStyle = `rgba(${r},${g},${b},${alpha})`;
        this.ctx.fillRect(
            -confetti.width/2 * alpha, 
            -confetti.height/2 * alpha, 
            confetti.width * alpha, 
            confetti.height * alpha
        );

        this.ctx.restore();
    }
}

export default Confetti;