class AudioVisualizer {
    constructor() {
        this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
        this.setupVisualizer();
        this.isActive = false;
        this.gradientColors = [
            ['#FF0088', '#FF00FF'],
            ['#00FF88', '#00FFFF'],
            ['#FF8800', '#FFFF00'],
            ['#0088FF', '#00FF88']
        ];
        this.currentGradient = 0;
    }

    setupVisualizer() {
        this.waveform = document.getElementById('waveform');
        if (!this.waveform) {
            console.error('Waveform element not found');
            return;
        }
        this.waveBars = [];
        this.createWaveBars();
        
        this.canvas = document.createElement('canvas');
        this.canvas.className = 'particle-canvas';
        this.waveform.parentElement.appendChild(this.canvas);
        this.ctx = this.canvas.getContext('2d');
        this.particles = [];
        
        this.resizeCanvas();
        window.addEventListener('resize', () => this.resizeCanvas());
    }

    createWaveBars() {
        // Clear existing bars
        this.waveform.innerHTML = '';
        
        // Create new bars
        for (let i = 0; i < 128; i++) {
            const bar = document.createElement('div');
            bar.className = 'wave-bar';
            this.waveform.appendChild(bar);
            this.waveBars.push(bar);
        }
    }

    resizeCanvas() {
        const container = this.waveform.parentElement;
        this.canvas.width = container.offsetWidth;
        this.canvas.height = container.offsetHeight;
    }

    start(audioContext, stream) {
        this.analyser = audioContext.createAnalyser();
        const source = audioContext.createMediaStreamSource(stream);
        source.connect(this.analyser);
        
        this.analyser.fftSize = 256;
        this.bufferLength = this.analyser.frequencyBinCount;
        this.dataArray = new Uint8Array(this.bufferLength);
        
        this.isActive = true;
        this.animate();
    }

    stop() {
        this.isActive = false;
        // Reset bars to default state
        this.waveBars.forEach(bar => {
            bar.style.height = '2px';
            bar.style.transform = 'scaleY(1)';
        });
        // Clear particles
        this.particles = [];
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
    }

    createParticle(x, y, velocity) {
        return {
            x,
            y,
            velocity,
            radius: Math.random() * 3 + 1,
            life: 255,
            color: this.getCurrentColor()
        };
    }

    getCurrentColor() {
        const gradient = this.gradientColors[this.currentGradient];
        return gradient[Math.floor(Math.random() * gradient.length)];
    }

    updateParticles() {
        for (let i = this.particles.length - 1; i >= 0; i--) {
            const particle = this.particles[i];
            particle.y -= particle.velocity;
            particle.life -= 5;

            if (particle.life <= 0) {
                this.particles.splice(i, 1);
            }
        }
    }

    drawParticles() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        
        this.particles.forEach(particle => {
            this.ctx.beginPath();
            this.ctx.arc(particle.x, particle.y, particle.radius, 0, Math.PI * 2);
            this.ctx.fillStyle = `${particle.color}${particle.life.toString(16)}`;
            this.ctx.fill();
        });
    }

    animate() {
        if (!this.isActive) return;

        // Get frequency data
        this.analyser.getByteFrequencyData(this.dataArray);
        
        // Cycle through gradients
        if (Date.now() % 50 === 0) {
            this.currentGradient = (this.currentGradient + 1) % this.gradientColors.length;
        }

        // Update bars and create particles
        for (let i = 0; i < this.bufferLength; i++) {
            const value = this.dataArray[i];
            const percent = value / 255;
            const height = (percent * 100) + 2;
            
            // Update bar
            const bar = this.waveBars[i];
            bar.style.height = `${height}px`;
            bar.style.transform = `scaleY(${1 + percent})`;
            
            // Generate gradient
            const gradientPair = this.gradientColors[this.currentGradient];
            const gradient = `linear-gradient(180deg, 
                ${gradientPair[0]} ${percent * 100}%, 
                ${gradientPair[1]} 100%)`;
            bar.style.backgroundImage = gradient;

            // Add particles on high frequencies
            if (value > 150 && Math.random() > 0.9) {
                const x = (i / this.bufferLength) * this.canvas.width;
                const y = this.canvas.height;
                this.particles.push(this.createParticle(x, y, 2 + Math.random() * 2));
            }
        }

        // Update and draw particles
        this.updateParticles();
        this.drawParticles();

        requestAnimationFrame(() => this.animate());
    }
}